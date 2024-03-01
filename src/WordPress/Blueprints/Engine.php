<?php

namespace WordPress\Blueprints;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;

class Engine {

	public function __construct(
		protected BlueprintParser $parser,
		protected BlueprintCompiler $compiler,
		public readonly BlueprintRunner $runner
	) {
	}

	public function runBlueprint( string|object $rawBlueprint, EventSubscriberInterface $progressSubscriber = null ) {
		$blueprint = $this->parser->parse( $rawBlueprint );
		$compiled = $this->compiler->compile( $blueprint );
		$compiled->progressTracker->events->addSubscriber( $progressSubscriber );

		return $this->runner->run( $compiled );
	}

}
