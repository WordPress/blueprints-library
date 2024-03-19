<?php

namespace WordPress\Blueprints\Cache;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FileCache implements CacheInterface {
	private $filesystem;

	private $cacheDirectory;

	public function __construct( $cacheDirectory = null ) {
		$this->cacheDirectory = $cacheDirectory;
		// initialize the Filesystem component
		$this->filesystem = new Filesystem();

		if ( $this->cacheDirectory === null ) {
			// Find user's home directory
			$homeDirectory = getenv( 'HOME' ) ?: getenv( 'USERPROFILE' );
			if ( ! $homeDirectory || ! is_writeable( $homeDirectory ) ) {
				throw new \Exception( "Could not find user's home directory" );
			}

			// Defining the cache directory
			$this->cacheDirectory = $homeDirectory . DIRECTORY_SEPARATOR . '.wp';
		}

		// Creating the cache directory if it does not exist
		if ( ! $this->filesystem->exists( $this->cacheDirectory ) ) {
			try {
				$this->filesystem->mkdir( $this->cacheDirectory );
			} catch ( IOExceptionInterface $exception ) {
				echo 'An error occurred while creating your cache directory at ' . $exception->getPath();
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$filepath = $this->getFilePathForKey( $key );
		if ( ! file_exists( $filepath ) ) {
			return $default;
		}

		$data = file_get_contents( $filepath );

		return unserialize( $data );
	}

	public function set( $key, $value, $ttl = null ): bool {
		$filepath = $this->getFilePathForKey( $key );
		$data     = serialize( $value );

		return file_put_contents( $filepath, $data ) !== false;
	}

	public function delete( $key ): bool {
		$filepath = $this->getFilePathForKey( $key );
		if ( file_exists( $filepath ) ) {
			return unlink( $filepath );
		}

		return true;
	}

	public function clear(): bool {
		$files = glob( $this->cacheDirectory . '/*' );
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
	}

	public function getMultiple( $keys, $default = null ) {
		$result = array();
		foreach ( $keys as $key ) {
			$result[ $key ] = $this->get( $key, $default );
		}

		return $result;
	}

	public function setMultiple( $values, $ttl = null ): bool {
		$result = true;
		foreach ( $values as $key => $value ) {
			$result = $result && $this->set( $key, $value, $ttl );
		}

		return $result;
	}

	public function deleteMultiple( $keys ): bool {
		$result = true;
		foreach ( $keys as $key ) {
			$result = $result && $this->delete( $key );
		}

		return $result;
	}

	public function has( $key ): bool {
		$filepath = $this->getFilePathForKey( $key );

		return file_exists( $filepath );
	}

	private function getFilePathForKey( $key ) {
		return $this->cacheDirectory . DIRECTORY_SEPARATOR . sha1( $key );
	}
}
