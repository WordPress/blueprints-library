<?php

namespace WordPress\Blueprints\Runtime;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;
use function WordPress\Blueprints\join_paths;

class Runtime implements RuntimeInterface {

	public $fs;
	protected $documentRoot;

	public function __construct(
		string $documentRoot
	) {
		$this->documentRoot = $documentRoot;
		$this->fs           = new Filesystem();
		if ( ! file_exists( $this->getDocumentRoot() ) ) {
			$this->fs->mkdir( $this->getDocumentRoot() );
		}
		if ( ! file_exists( $this->getTempRoot() ) ) {
			$this->fs->mkdir( $this->getTempRoot() );
		}
	}

	// @TODO: public function getTmpDir(): string {
	// @TODO: should this class mediate network requests?

	// @TODO: Move these filesystem operations to a separate class
	// Maybe ExecutionContext? Or a separate Filesystem class?
	public function getDocumentRoot(): string {
		return $this->documentRoot;
	}

	/**
	 * @param string $path
	 */
	public function resolvePath( $path ): string {
		return Path::makeAbsolute( $path, $this->getDocumentRoot() );
	}

	public function withTemporaryDirectory( $callback ) {
		$path = $this->fs->tempnam( $this->getTempRoot(), 'tmpdir' );
		$this->fs->remove( $path );
		$this->fs->mkdir( $path );
		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}

	public function withTemporaryFile( $callback, $suffix = null ) {
		$path = $this->fs->tempnam( $this->getTempRoot(), 'tmpfile', $suffix );
		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}

	public function getTempRoot() {
		// Store tmp files inside document root because in some runtime environments,
		// `/tmp` may be on another filesystem and we couldn't move files across filesystems
		// without a slow recursive copy.
		return join_paths( $this->getDocumentRoot(), '/tmp' );
		// return sys_get_temp_dir();
	}

	// @TODO: Move this to a separate class
	/**
	 * @param mixed[]|null $env
	 * @param float        $timeout
	 */
	public function evalPhpInSubProcess(
		$code,
		$env = null,
		$input = null,
		$timeout = 60
	) {
		return $this->runShellCommand(
			array(
				'php',
				'-r',
				'?>' . $code,
			),
			null,
			array_merge(
				array(
					'DOCROOT' => $this->getDocumentRoot(),
				),
				$env ?? array()
			),
			$input,
			$timeout
		);
	}

	// @TODO: Move this to a separate class
	/**
	 * @param mixed[]      $command
	 * @param string|null  $cwd
	 * @param mixed[]|null $env
	 * @param float        $timeout
	 */
	public function runShellCommand(
		$command,
		$cwd = null,
		$env = null,
		$input = null,
		$timeout = 60
	) {
		$process = $this->startProcess(
			$command,
			$cwd,
			$env,
			$input,
			$timeout
		);
		$process->start();
		$process->wait();
		if ( $process->getExitCode() !== 0 ) {
			throw new ProcessFailedException( $process );
		}

		return $process->getOutput();
	}

	/**
	 * @param mixed[]      $command
	 * @param string|null  $cwd
	 * @param mixed[]|null $env
	 * @param float        $timeout
	 */
	public function startProcess(
		$command,
		$cwd = null,
		$env = null,
		$input = null,
		$timeout = 60
	): Process {
		$cwd = $cwd ?? $this->getDocumentRoot();

		return new Process(
			$command,
			$cwd,
			$env,
			$input,
			$timeout
		);
	}
}
