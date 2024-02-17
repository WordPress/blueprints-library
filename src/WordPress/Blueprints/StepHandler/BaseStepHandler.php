<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Context\ExecutionContext;
use WordPress\Blueprints\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

abstract class BaseStepHandler {
	protected ResourceManager $resourceManager;

	protected RuntimeInterface $runtime;

	public function setResourceMap( ResourceManager $map ) {
		$this->resourceManager = $map;
	}

	protected function getResource( $declaration ) {
		return $this->resourceManager[ $declaration ];
	}

	public function setRuntime( RuntimeInterface $runtime ): void {
		$this->runtime = $runtime;
	}

	protected function getRuntime(): RuntimeInterface {
		return $this->runtime;
	}

}
