<?php

namespace WordPress\Blueprints\Runtime;

use Symfony\Component\Process\Process;

class NativePHPRuntime implements RuntimeInterface {

	public function __construct(
		protected string $documentRoot
	) {
	}


	public function getDocumentRoot(): string {
		return $this->documentRoot;
	}

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
