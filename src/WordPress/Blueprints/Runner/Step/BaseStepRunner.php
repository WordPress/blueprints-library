<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

abstract class BaseStepRunner implements StepRunnerInterface {
	protected $resourceManager;

	protected $runtime;

	/**
	 * @param \WordPress\Blueprints\Resources\ResourceManager $map
	 */
	public function setResourceManager( $map ) {
		$this->resourceManager = $map;
	}

	protected function getResource( $declaration ) {
		return $this->resourceManager->getStream( $declaration );
	}

	/**
	 * @param \WordPress\Blueprints\Runtime\RuntimeInterface $runtime
	 */
	public function setRuntime( $runtime ) {
		$this->runtime = $runtime;
	}

	protected function getRuntime(): RuntimeInterface {
		return $this->runtime;
	}

	public function getDefaultCaption( $input ) {
		return null;
	}
}
