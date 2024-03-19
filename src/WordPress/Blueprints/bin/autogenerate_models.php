<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Jane\Component\JsonSchema\Console\Loader\SchemaLoader;
use Jane\Component\JsonSchema\Jane;
use Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use Jane\Component\JsonSchema\Registry\Registry;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpNamespace;

$schemaPath = __DIR__ . '/../schema.json';
if ( ! file_exists( $schemaPath ) ) {
	throw new \RuntimeException( "Schema file $schemaPath does not exist" );
}

$targetNamespace = 'WordPress\\Blueprints\\Model\\DataClass';
$targetDirectory = __DIR__ . '/../Model/DataClass';
if ( ! file_exists( $targetDirectory ) ) {
	mkdir( $targetDirectory, 0777, true );
}
$targetDirectory = realpath( $targetDirectory );
if ( ! $targetDirectory ) {
	throw new \RuntimeException( "Target directory $targetDirectory does not exist" );
}

$jane         = Jane::build(
	array(
		'reference'  => '#',
		'validation' => true,
		'strict'     => false,
	)
);
$registry     = new Registry();
$schemaLoader = new SchemaLoader();
$registry->addSchema(
	$schemaLoader->resolve(
		$schemaPath,
		array(
			'namespace'  => $targetNamespace,
			'directory'  => $targetDirectory,
			'root-class' => 'Blueprint',
			'strict'     => false,
		)
	)
);
$context = $jane->createContext( $registry );
$jane->generate( $registry );

if ( ! file_exists( $targetDirectory ) ) {
	mkdir( $targetDirectory, 0777, true );
}

$schema      = $context->getRegistry()->getSchemas()[0];
$janeClasses = $schema->getClasses();

// Every group of classes with a discriminator and oneOf should have
// an interface that all the classes in the group implement.
$netteInterfaces       = array();
$shouldImplement       = array();
$interfaceImplementors = array();
foreach ( $janeClasses as $ref => $class ) {
	// If $ref ends with /([^/]+)/oneOf/\d, extract the first group
	if ( 1 !== preg_match( '/\/([^\/]+)\/oneOf\/\d+$/', $ref, $matches ) ) {
		continue;
	}

	$interfaceName                             = $matches[1] . 'Interface';
	$shouldImplement[ $class->getName() ]      = $interfaceName;
	$interfaceImplementors[ $interfaceName ][] = $class->getName();

	if ( array_key_exists( $interfaceName, $netteInterfaces ) ) {
		continue;
	}

	$namespace                                = new PhpNamespace( $targetNamespace );
	$interface                                = $namespace->addInterface( $interfaceName );
	$netteInterfaces[ $interface->getName() ] = $interface;
}

/**
 * Generate a class that provides information about the implementations of
 * the interfaces we just created.
 */
$modelInfoClass = ( new PhpNamespace( $targetNamespace ) )->addClass( 'ModelInfo' );
foreach ( $interfaceImplementors as $interfaceName => $implementors ) {
	$implementorsClassExpressions = array_map(
		function ( $implementor ) {
			return $implementor . '::class';
		},
		$implementors
	);
	$modelInfoClass->addMethod( 'get' . $interfaceName . 'Implementations' )
		->setStatic( true )
		->setReturnType( 'array' )
		->setBody( "return [\n\t" . implode( ",\n\t", $implementorsClassExpressions ) . "\n];" );
}

/**
 * Jane annotates discriminated properties with their full union type:
 *
 * /**
 *   * @var string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource
 *   * public $pluginZipFile;
 *   * /
 *
 * However, we don't want to use the union type. We want to use the interface type:
 *
 * /**
 *  * @var string|ResourceReferenceInterface
 *  * /
 *
 * The structured type definition data seems to be lost at this point.
 * Instead of recovering it, let's go for a simplified solution and
 * simply str_replace the union type with the interface type.
 *
 * For that, we'll need a map of replacements.
 */
$unionReplacements = array();
foreach ( $interfaceImplementors as $interfaceName => $implementors ) {
	$unionReplacements[ implode( '|', $implementors ) ] = $interfaceName;

	$arrayUnion                                       = array_map(
		function ( $implementor ) {
			return $implementor . '[]';
		},
		$implementors
	);
	$unionReplacements[ implode( '|', $arrayUnion ) ] = $interfaceName . '[]';
}

function fixTypeHint( string $typeHint, array $replacements ): string {
	foreach ( $replacements as $union => $replacement ) {
		$typeHint = str_replace( $union, $replacement, $typeHint );
	}

	return $typeHint;
}

/**
 * Jane did the heavy lifting of parsing the JSON Schema and inferring
 * the PHP classes. However, the classes it generates are too opinionated.
 * Let's convert them to Nette classes and customize them to our needs.
 */
$typeHintMap  = array();
$netteClasses = array();
/** @var ClassType[] $netteClasses */
foreach ( $janeClasses as $ref => $janeClass ) {
	$namespace    = new PhpNamespace( $targetNamespace );
	$class        = $namespace->addClass( $janeClass->getName() );
	$hasInterface = isset( $shouldImplement[ $janeClass->getName() ] );
	if ( $hasInterface ) {
		$class->addImplement( $targetNamespace . '\\' . $shouldImplement[ $janeClass->getName() ] );
	}
	$typeHintMap[ $janeClass->getName() ] = array();
	foreach ( $janeClass->getProperties() as $janeProperty ) {
		// Add the property to the class.
		$property    = $class->addProperty( $janeProperty->getName() );
		$description = $janeProperty->getDescription();
		$typeHint    = $janeProperty->getType()->getTypeHint( '' );
		$docTypeHint = $janeProperty->getType()->getDocTypeHint( '' );
		if ( strpos( $docTypeHint, $targetNamespace ) !== false ) {
			// Jane prepends "\$namespace\Model\" to type hints, let's remove that.
			$docTypeHint = str_replace( '\\' . $targetNamespace . '\\Model\\', '', $docTypeHint );
			// Let's replace the lengthy union types with the interface types.
			foreach ( $unionReplacements as $union => $replacement ) {
				$docTypeHint = str_replace( $union, $replacement, $docTypeHint );
			}

			// The JSON mapper we use has a bug and will throw an error
			// if a union lists primitive types before object types. Let's
			// fix that by moving the object types to the front.
			$subTypes = explode( '|', $docTypeHint );
			if ( count( $subTypes ) > 1 ) {
				$phpPrimitiveTypes = array( 'string', 'int', 'float', 'bool', 'array', 'object' );
				$finalTypes        = array();
				foreach ( $phpPrimitiveTypes as $phpPrimitiveType ) {
					if ( in_array( $phpPrimitiveType, $subTypes ) ) {
						$finalTypes[] = $phpPrimitiveType;
					}
				}
				$finalTypes  = array_merge( $finalTypes, array_diff( $subTypes, $phpPrimitiveTypes ) );
				$docTypeHint = implode( '|', $finalTypes );
			}
		}
		if ( preg_match( '#^array<string, [^>]+>$#', $docTypeHint ) ) {
			$docTypeHint = '\\ArrayObject';
		}
		if ( $description ) {
			$property->addComment( $description );
		}
		$typeHintMap[ $janeClass->getName() ][ $janeProperty->getName() ] = $docTypeHint;
		$property->addComment( '@var ' . $docTypeHint . '' );

		// Add a setter
		$setter = $class->addMethod( 'set' . ucfirst( $janeProperty->getName() ) );

		$setterArg   = $setter->addParameter( $janeProperty->getName() );
		$argTypeHint = $janeProperty->getType()->getTypeHint( $targetNamespace ) . '';
		if ( $argTypeHint ) {
			if ( strpos( $argTypeHint, '\\' ) !== false ) {
				$argTypeHint = $targetNamespace . '\\' . str_replace(
					'\\' . $targetNamespace . '\\Model\\',
					'',
					$argTypeHint
				);
			}
			$setterArg->setType( $argTypeHint );
		}
		$setter->setBody(
			'$this->' . $janeProperty->getName() . ' = $' . $janeProperty->getName() . ";\n" .
			'return $this;'
		);

		$schema = $janeProperty->getObject();
		if ( $schema instanceof JsonSchema ) {
			if ( $schema->getConst() ) {
				$property->setValue( $schema->getConst() );
				// Assume that a class with an interface uses a const property
				// as the discriminator field. This is a naive assumption and
				// we should actually check the schema for the discriminator
				// field. However, that would add complexity to this script
				// so let's keep it simple for now.
				if ( $hasInterface ) {
					$class->addConstant( 'DISCRIMINATOR', $schema->getConst() );
					// Method 'addConstant' by default sets const visibility to 'public', but PHP 7.0 does not like it.
					// So, we have to manually set it back to null.
					$class->getConstants()['DISCRIMINATOR']->setVisibility( null );
				}
			} elseif ( $schema->getDefault() !== null ) {
				// Don't set "null" as the default value since it's already a default
				// value of all class properties.
				$property->setValue( $schema->getDefault() );
			} elseif ( $schema->getType() === 'array' ) {
				$property->setValue( array() );
			}
		}
	}
	$netteClasses[ $class->getName() ] = $class;
}

/**
 * We're finished, yay! Let's write the generated classes to the disk.
 */
$netteOutput = array_merge( $netteInterfaces, $netteClasses, array( $modelInfoClass ) );
foreach ( glob( $targetDirectory . '/*.php' ) as $file ) {
	unlink( $file );
}
/** @var ClassType[]|InterfaceType[] $netteOutput */
foreach ( $netteOutput as $entity ) {
	$filename = $targetDirectory . '/' . $entity->getName() . '.php';
	file_put_contents( $filename, "<?php\n\n" . $entity->getNamespace() );
}

echo 'Generated ' . count( $netteOutput ) . " classes in $targetDirectory\n";
