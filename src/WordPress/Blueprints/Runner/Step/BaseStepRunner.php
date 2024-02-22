<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Resource\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;
use WordPress\FileManager\FileManager;

abstract class BaseStepRunner implements StepRunnerInterface {
	protected ResourceManager $resourceManager;

	protected RuntimeInterface $runtime;

    protected FileManager $fileManager;

    function __construct(RuntimeInterface $runtime) {
        $this->fileManager = new FileManager($runtime);
    }

	public function setResourceManager( ResourceManager $map ) {
		$this->resourceManager = $map;
	}

	protected function getResource( $declaration ) {
		return $this->resourceManager->getStream( $declaration );
	}

//    TODO would advise to set runtime at initialization only
	public function setRuntime( RuntimeInterface $runtime ): void {
		$this->runtime = $runtime;
	}

	protected function getRuntime(): RuntimeInterface {
		return $this->runtime;
	}

	protected function getDefaultCaption( $input ): ?string {
		return null;
	}
}