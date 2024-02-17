<?php

namespace WordPress\Blueprints\Runtime;

use Symfony\Component\Process\Process;

interface RuntimeInterface {

	public function startProcess(
		array $command,
		?string $cwd = null,
		?array $env = null,
		mixed $input = null,
		?float $timeout = 60
	): Process;

}
