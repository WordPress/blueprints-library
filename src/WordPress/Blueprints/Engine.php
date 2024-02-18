<?php

namespace WordPress\Blueprints;

use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Model\BlueprintParser;
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
		if ( is_string( $rawBlueprint ) ) {
			$blueprint = $this->parser->fromJson( $rawBlueprint );
		} elseif ( $rawBlueprint instanceof BlueprintBuilder ) {
			$blueprint = $this->parser->fromBuilder( $rawBlueprint );
		} elseif ( is_object( $rawBlueprint ) ) {
			$blueprint = $this->parser->fromObject( $rawBlueprint );
		} else {
			throw new \InvalidArgumentException( 'Unsupported $rawBlueprint type. Use a JSON string, a parsed JSON object, or a BlueprintBuilder instance.' );
		}

		$compiled = $this->compiler->compile( $blueprint );

		return $this->runner->run( $compiled );
	}

}
