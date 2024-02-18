<?php

namespace WordPress\Blueprints\StepRunner;

use WordPress\Blueprints\Context\ExecutionContext;
use WordPress\Blueprints\Model\DataClass\StepInterface;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\ResourceManager;
use WordPress\Blueprints\Runtime\RuntimeInterface;

abstract class BaseStepRunner implements StepRunnerInterface {
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

//	public function run( StepInterface $input = null, Tracker $tracker = null ) {
//		if ( ! $tracker ) {
//			$tracker = new Tracker();
//		}
//		$inputType = static::getStepClass();
//		if ( ! ( $input instanceof $inputType ) ) {
//			throw new \InvalidArgumentException( "Expected input of type $inputType, got " . get_class( $input ) );
//		}
//		$initialCaption = $input->progress->caption ?? $this->getDefaultCaption( $input );
//		if ( $initialCaption ) {
//			$tracker->setCaption( $initialCaption );
//		}
//
//		return $this->dorun( $input, $tracker );
//	}

	protected function getDefaultCaption( $input ): string|null {
		return null;
	}

//	abstract protected function dorun( $input, Tracker $tracker );

}
