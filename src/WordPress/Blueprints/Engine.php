<?php

namespace WordPress\Blueprints;

use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;

class Engine {

	public function __construct(
		protected BlueprintParser $parser,
		protected BlueprintCompiler $compiler,
		protected BlueprintRunner $runner
	) {
	}

	public function runBlueprint( string|object $rawBlueprint ) {
		$blueprint = $this->parser->parse( $rawBlueprint );
		$compiled = $this->compiler->compile( $blueprint );

		return $this->runner->run( $compiled );
	}

}
