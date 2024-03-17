<?php

namespace WordPress\Blueprints;

use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Compile\CompiledBlueprint;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;

class Engine {

	/**
	 * @var BlueprintRunner
	 */
	public $runner;

	/**
	 * @var BlueprintParser
	 */
	protected $parser;

	/**
	 * @var BlueprintCompiler
	 */
	protected $compiler;

	public function __construct(
		BlueprintParser $parser,
		BlueprintCompiler $compiler,
		BlueprintRunner $runner
	) {
		$this->runner = $runner;
		$this->compiler = $compiler;
		$this->parser = $parser;
	}

	public function parseAndCompile( $raw_blueprint ) {
		$blueprint = $this->parser->parse( $raw_blueprint );

		return $this->compiler->compile( $blueprint );
	}

	public function run( CompiledBlueprint $compiled_blueprint ) {
		return $this->runner->run( $compiled_blueprint );
	}
}
