<?php

namespace WordPress\Blueprints\Runner\Blueprint;

use Symfony\Component\EventDispatcher\EventDispatcher;
use WordPress\Blueprints\Compile\CompiledBlueprint;
use WordPress\Blueprints\Compile\CompiledStep;
use WordPress\Blueprints\Compile\StepSuccess;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\Runtime\RuntimeInterface;

class BlueprintRunner {

	public readonly EventDispatcher $events;

	public function __construct(
		protected RuntimeInterface $runtime,
		protected $resourceManagerFactory,
	) {
		$this->events = new EventDispatcher();
	}

	public function run( CompiledBlueprint $blueprint ) {
		$resourceManagerFactory = $this->resourceManagerFactory;
		$resourceManager = $resourceManagerFactory();
		$resourceManager->enqueue(
			$blueprint->compiledResources
		);
		foreach ( $blueprint->compiledSteps as $compiledStep ) {
			$compiledStep->runner->setRuntime( $this->runtime );
			$compiledStep->runner->setResourceManager( $resourceManager );
		}

		// Run, store results
		$results = [];
		foreach ( $blueprint->compiledSteps as $k => $compiledStep ) {
			/** @var CompiledStep $compiledStep */
			try {
				$results[ $k ] = new StepSuccess(
					$compiledStep->step,
					$compiledStep->runner->run( $compiledStep->step, new Tracker() )
				);
			} catch ( \Exception $e ) {
				$results[ $k ] = $e;
				if ( $compiledStep->step->continueOnError !== true ) {
					throw new BlueprintRunnerException(
						"Error when executing step {$compiledStep->step->step} (number $k on the list)",
						0,
						$results[ $k ]
					);
				}
			}
		}

		return $results;
	}

}
