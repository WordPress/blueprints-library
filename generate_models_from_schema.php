<?php

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

require __DIR__ . '/vendor/autoload.php';

class ModelGenerator {

	protected $refMap = [];

	public $output = [];

	public function __construct( protected $schema, protected $namespace = 'WordPress\Blueprints\Models' ) {

	}

	protected function getByRef( $ref ) {
		if ( strpos( $ref, '#' ) === 0 ) {
			$ref = substr( $ref, 2 );
		}
		$parts = explode( '/', $ref );
		$subSchema = $this->schema;
		foreach ( $parts as $part ) {
			if ( ! isset( $subSchema[ $part ] ) ) {
				throw new \Exception( 'Could not find ref ' . $ref );
			}
			$subSchema = $subSchema[ $part ];
		}

		return $subSchema;
	}

	public function generateModelByRef( $ref, $interface = null ) {
		$definition = $this->getByRef( $ref );
		$name = $this->refToClassName( $ref );

		return $this->generateModel( $name, $definition, $ref, $interface );
	}

	public function generateModel( $name, $definition, $ref = null, $interface = null ) {
		if ( $definition['type'] !== 'object' ) {
			throw new \Exception( 'Only object types are supported' );
		}

		// We may want an interface, not a class
		if ( isset( $definition['anyOf'] ) ) {
			$refersOnlyToOtherModels = true;
			foreach ( $definition['anyOf'] as $anyOf ) {
				if ( ! isset( $anyOf['$ref'] ) ) {
					$refersOnlyToOtherModels = false;
					break;
				}
			}
			if ( $refersOnlyToOtherModels ) {
				$interface = new InterfaceType( $name, new Nette\PhpGenerator\PhpNamespace( $this->namespace ) );
				$this->output[] = $interface;
				$this->refMap[ $ref ] = $interface;
				foreach ( $definition['anyOf'] as $anyOf ) {
					$this->generateModelByRef( $anyOf['$ref'], $interface );
				}

				return;
			}
		}

		$class = new ClassType( $name, new Nette\PhpGenerator\PhpNamespace( $this->namespace ) );
		$this->output[] = $class;
		if ( $interface ) {
			$class->addImplement( $interface );
		}
		if ( $ref ) {
			$this->refMap[ $ref ] = $class;
		}
		foreach ( $definition['properties'] as $key => $property ) {
			if ( $key === '$schema' ) {
				continue;
			}
			$classProperty = $class
				->addProperty( $key )
				->setVisibility( 'public' );

			if (
				$property['type'] === "object"
				&& isset( $property['properties'] )
			) {
				$nestedClass = $this->generateModel( $name . ucfirst( $key ), $property );
				$classProperty->setComment(
					'@var ' . $nestedClass->getName()
				);
			} elseif ( $property['type'] === "array" ) {
				if ( isset( $property['items']['$ref'] ) ) {
					$this->generateModelByRef( $property['items']['$ref'] );
					$classProperty->setComment(
						'@var ' . $nestedClass->getName() . '[]'
					);
				}
			} else {
				$classProperty->setComment(
					'@var ' . $this->inferPhpType( $property )
				);
			}
			$class->addMethod( 'set' . ucfirst( $key ) )
				->setBody( '$this->' . $key . ' = $' . $key . ';' . PHP_EOL . 'return $this;' )
				->addParameter( $key );
		}

		return $class;
	}

	public function inferPhpType( $property ) {
		if ( isset( $property['$ref'] ) ) {
			return $this->refToClassName( $property['$ref'], true );
		}
		if ( ! isset( $property['type'] ) ) {
			throw new \Exception( 'Could not infer type of property ' . json_encode( $property ) );
		}
		if ( $property['type'] === "string" ) {
			return 'string';
		}
		if ( $property['type'] === "integer" ) {
			return 'int';
		}
		if ( $property['type'] === "number" ) {
			return 'float';
		}
		if ( $property['type'] === "boolean" ) {
			return 'bool';
		}
		if ( $property['type'] === "array" ) {
			if ( isset( $property['items'] ) ) {
				return $this->inferPhpType( $property['items'] ) . '[]';
			}

			return 'array';
		}
		if (
			$property['type'] === "object"
			&& ! isset( $property['properties'] )
			&& isset( $property['additionalProperties'] )
			&& is_array( $property['additionalProperties'] )
			&& (
				! array_key_exists( 'type', $property['additionalProperties'] )
				|| in_array( $property['additionalProperties']['type'],
					[ 'string', 'integer', 'number', 'boolean' ] )
			)
		) {
			// Object with just additional properties is an assoc array
			return 'array';
		}
	}

	private function refToClassName( string $ref, $fullyQualified = false ) {
		$pieces = explode( '/', $ref );

		$className = ucfirst( end( $pieces ) );
		if ( $fullyQualified ) {
			$className = $this->namespace . '\\' . $className;
		}

		return $className;
	}

}

$json = json_decode( file_get_contents( './src/WordPress/Blueprints/schema.json' ), true );

$generator = new ModelGenerator( $json );
$generator->generateModelByRef( $json['$ref'] );
echo $generator->output[3] . '';
