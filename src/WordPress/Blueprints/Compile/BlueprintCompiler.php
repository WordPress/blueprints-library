<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Runtime\RuntimeInterface;

class BlueprintCompiler {

	public function __construct(
		protected $stepRunnerFactory,
	) {
	}

	public function compile( Blueprint $blueprint ): CompiledBlueprint {
		return new CompiledBlueprint(
			$this->compileSteps( $blueprint ),
			$this->compileResources( $blueprint )
		);
	}

	protected function compileSteps( Blueprint $blueprint ) {
		$stepRunnerFactory = $this->stepRunnerFactory;
		// Compile, ensure all the runners may be created and configured
		$compiledSteps = [];
		foreach ( $blueprint->steps as $step ) {
			$runner = $stepRunnerFactory( $step->step );
			/** @var $runner \WordPress\Blueprints\Runner\Step\BaseStepRunner */
			$compiledSteps[] = new CompiledStep(
				$step,
				$runner
			);
		}

		return $compiledSteps;
	}

	protected function compileResources( Blueprint $blueprint ) {
		$resources = [];
		$this->findResources( $blueprint, $resources );

		return $resources;
	}

	// Find all the resources in the blueprint
	protected function findResources( $blueprintFragment, &$resources, $path = '' ) {
		if ( $blueprintFragment instanceof FileReferenceInterface ) {
			$resources[ $path ] = $blueprintFragment;
		} elseif ( is_object( $blueprintFragment ) ) {
			foreach ( get_object_vars( $blueprintFragment ) as $key => $value ) {
				$this->findResources( $value, $resources, $path . '->' . $key );
			}
		} elseif ( is_array( $blueprintFragment ) ) {
			foreach ( $blueprintFragment as $k => $v ) {
				$this->findResources( $v, $resources, $path . '[' . $k . ']' );
			}
		} else {
			return $blueprintFragment;
		}
	}

}
