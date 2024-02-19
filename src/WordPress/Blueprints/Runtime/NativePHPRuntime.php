<?php

namespace WordPress\Blueprints\Runtime;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use function WordPress\Blueprints\join_paths;

class NativePHPRuntime implements RuntimeInterface {

	public Filesystem $fs;

	public function __construct(
		protected string $documentRoot
	) {
		$this->fs = new Filesystem();
		$this->fs->mkdir( $this->documentRoot );
	}

	// @TODO: public function getTmpDir(): string {
	// @TODO: should this class mediate network requests?

	// @TODO: Move these filesystem operations to a separate class
	//        Maybe ExecutionContext? Or a separate Filesystem class?
	public function getDocumentRoot(): string {
		return $this->documentRoot;
	}

	public function resolvePath( string $path ): string {
		if ( ! strlen( $path ) ) {
			return $this->getDocumentRoot();
		}
		if ( $path[0] === '/' ) {
			return $path;
		}

		return join_paths( $this->getDocumentRoot(), $path );
	}

	public function withTemporaryDirectory( $callback ) {
		$path = $this->fs->tempnam( sys_get_temp_dir(), 'tmpdir' );
		$this->fs->remove( $path );
		$this->fs->mkdir( $path );
		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}

	}

	public function withTemporaryFile( $callback, $suffix = null ) {
		$path = $this->fs->tempnam( sys_get_temp_dir(), 'tmpfile', $suffix );
		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}

	// @TODO: Move this to a separate class
	public function evalPhpInSubProcess(
		$code,
		?array $env = null,
		mixed $input = null,
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
		mixed $input = null,
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
		mixed $input = null,
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
