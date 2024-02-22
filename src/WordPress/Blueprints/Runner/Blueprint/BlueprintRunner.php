<?php

namespace WordPress\Blueprints\Runner\Blueprint;

use Symfony\Component\EventDispatcher\EventDispatcher;
use WordPress\Blueprints\Compile\CompiledBlueprint;
use WordPress\Blueprints\Compile\CompiledStep;
use WordPress\Blueprints\Compile\StepSuccess;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\Runtime\RuntimeInterface;

class BlueprintRunner {

	public EventDispatcher $events;
    protected RuntimeInterface $runtime;
    protected $resourceManagerFactory;

    public function __construct(
		RuntimeInterface $runtime,
		$resourceManagerFactory
	) {
        $this->resourceManagerFactory = $resourceManagerFactory;
        $this->runtime = $runtime;
        $this->events = new EventDispatcher();
	}

    // 1. compile
    // 2. DI ?
    // 3. handle resources - enqueue not working
    // 4. run steps

    // 4.b - fail strategies and snapshots, what happens when it failed, what is reverted?
    // 4.c how can it be corrected? will a strict error policy not interfere with this?

	public function run( CompiledBlueprint $blueprint ) {
		$resourceManagerFactory = $this->resourceManagerFactory;
        // TODO why factory? java much?
		$resourceManager        = $resourceManagerFactory();
        // this does the resource steps. Why not run in the same way the runCompiledSteps method works?
        // and just iterate over it?
        // maybe prepareRequiredResources
		$resourceManager->enqueue(
			$blueprint->compiledResources
		);
        // TODO initiate fileManager, tracker? something for metrics?
        // is the tracker a per step obj or a singleton?
		foreach ( $blueprint->compiledSteps as $compiledStep ) {
            // TODO set execution context
			$compiledStep->runner->setRuntime( $this->runtime );
			$compiledStep->runner->setResourceManager( $resourceManager );
		}

		// Run, store results
        return $this->runCompiledSteps($blueprint);
	}

    /**
     * @param CompiledBlueprint $blueprint
     * @return array
     * @throws BlueprintRunnerException
     */
    public function runCompiledSteps(CompiledBlueprint $blueprint): array
    {
        $results = [];
        foreach ($blueprint->compiledSteps as $k => $compiledStep) {
            /** @var CompiledStep $compiledStep */
            try {
                $results[$k] = new StepSuccess(
                    $compiledStep->step,
                    $compiledStep->runner->run($compiledStep->step, new Tracker())
                );
                // blueprint exeption? all exceptions?
            } catch (\Exception $e) {
                if ($compiledStep->step->continueOnError !== true) {
                    throw new BlueprintRunnerException(
                        //
                        "Error when executing step {$compiledStep->step->step} (number $k on the list)",
                        0,
                        $e
                    );
                }
                $results[$k] = $e;
            }
        }
        return $results;
    }
}
