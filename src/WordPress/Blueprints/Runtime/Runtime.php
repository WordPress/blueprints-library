<?php

namespace WordPress\Blueprints\Runtime;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;
use function WordPress\Blueprints\join_paths;

class Runtime implements RuntimeInterface {

	public Filesystem $fs;
    protected string $documentRoot;

    public function __construct(
		string $documentRoot
	) {
        $this->documentRoot = $documentRoot;
        $this->fs = new Filesystem();
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
	//        Maybe ExecutionContext? Or a separate Filesystem class?
	public function getDocumentRoot(): string {
		return $this->documentRoot;
	}

	public function resolvePath( string $path ): string {
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
//		return sys_get_temp_dir();
	}

	// @TODO: Move this to a separate class
	public function evalPhpInSubProcess(
		$code,
		?array $env = null,
		$input = null,
		?float $timeout = 60
	) {
		return $this->runShellCommand(
			[
				'php',
				'-r',
				'?>' . $code,
			],
			null,
			[
				'DOCROOT' => $this->getDocumentRoot(),
				...( $env ?? [] ),
			],
			$input,
			$timeout
		);
	}

	// @TODO: Move this to a separate class
	public function runShellCommand(
		array $command,
		?string $cwd = null,
		?array $env = null,
		$input = null,
		?float $timeout = 60
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

	public function startProcess(
		array $command,
		?string $cwd = null,
		?array $env = null,
		$input = null,
		?float $timeout = 60
	): Process {
		$cwd ??= $this->getDocumentRoot();

		return new Process(
			$command,
			$cwd,
			$env,
			$input,
			$timeout
		);
	}
}
