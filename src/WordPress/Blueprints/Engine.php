<?php

namespace WordPress\Blueprints;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Compile\CompiledBlueprint;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;

class Engine {

	public function __construct(
		protected BlueprintParser $parser,
		protected BlueprintCompiler $compiler,
		public readonly BlueprintRunner $runner
	) {
	}

	public function parseAndCompile( string|object $rawBlueprint ) {
		$blueprint = $this->parser->parse( $rawBlueprint );

		return $this->compiler->compile( $blueprint );
	}

	public function run( CompiledBlueprint $compiledBlueprint ) {
		return $this->runner->run( $compiledBlueprint );
	}

}
