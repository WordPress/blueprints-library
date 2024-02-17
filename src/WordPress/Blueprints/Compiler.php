<?php

namespace WordPress\Blueprints;

use Symfony\Component\EventDispatcher\EventDispatcher;
use WordPress\Blueprints\Resources\Resource;
use WordPress\Blueprints\StepHandler\Implementation\UnzipStepHandler;

class CompiledStep {
	public function __construct( public object $step, public array $resources ) {
	}

}

class CompiledFeatures {
	public function __construct(
		/** Should boot with support for network request via wp_safe_remote_get? */
		public bool $networking
	) {
	}
}


class CompiledVersion {
	public function __construct( public string $php, public string $wp ) {
	}
}

class CompiledBlueprint {
	public readonly EventDispatcher $events;

	public function __construct(
		/** The requested versions of PHP and WordPress for the blueprint */
		public CompiledVersion $versions,
		/** The requested PHP extensions to load */
		public array $phpExtensions,
		public CompiledFeatures $features,
		public array $steps
	) {
		$this->events = new EventDispatcher();
	}

	public function execute() {
		foreach ( $this->steps as $step ) {
			$step->step->execute();
		}
	}

}

class Compiler {

	public function compile( array $blueprint ) {
		return new CompiledBlueprint(
			new CompiledVersion(
				$blueprint['preferredVersions']['php'] ?? '8.0',
				$blueprint['preferredVersions']['wp'] ?? '6.4'
			),
			[ 'kitchen-sink' ],
			new CompiledFeatures(
				$blueprint['features']['networking'] ?? false
			),
			$this->compileSteps( $blueprint['steps'] )
		);
	}

	private function compileSteps( array $stepsJson ) {
		$compiledSteps = [];
		foreach ( $stepsJson as $stepJson ) {
			$compiledSteps[] = $this->compileStep( $stepJson );
		}

		return $compiledSteps;
	}

	private function compileStep( array $stepJson ) {
		if ( ! isset( $this->availableSteps[ $stepJson['step'] ] ) ) {
			throw new \Exception( "Step not found: " . $stepJson['step'] );
		}

		$stepClass      = $this->availableSteps[ $stepJson['step'] ];
		$stepInputClass = $stepClass::getInputClass();

		$resources = [];

		$args = $stepJson;
		unset( $args['step'] );
		$resourceArgs = $this->getStepResourceArgs( UnzipStepHandler::getInputClass() );
		foreach ( $resourceArgs as $resourceArgName ) {
			$arg         = $args[ $resourceArgName ];
			$resources[] = $args[ $resourceArgName ] = $this->compileResource( $arg );
		}

		$stepInput = new $stepInputClass( ...$args );
		$step      = new $stepClass( $stepInput );

		return new CompiledStep( $step, $resources );
	}

	private function compileResource( array $resourceJson ) {
		$resourceClass = $this->availableResources[ $resourceJson['resource'] ];

		$args = $resourceJson;
		unset( $args['resource'] );

		return new $resourceClass( ...$args );
	}

	private function getStepResourceArgs( string $stepClass ) {
		$resourceArgs = [];
		$r            = new \ReflectionClass( $stepClass );
		foreach ( $r->getProperties() as $prop ) {
			$propType = $prop->getType()->getName();
			if ( $propType === Resource::class ) {
				$resourceArgs[] = $prop->getName();
			}
		}

		return $resourceArgs;
	}

	private function assertValid( object $blueprint ) {
		// @TODO: Inject using service container
		$validator = new \JsonSchema\Validator;
		$schema    = json_decode( file_get_contents( __DIR__ . '/schema.json' ) );
		$validator->validate( $blueprint, $schema );
		if ( ! $validator->isValid() ) {
			print_r( $validator->getErrors() );
			throw new \Exception( "Invalid blueprint!" );
		}
	}
}
