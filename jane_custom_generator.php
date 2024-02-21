<?php

require 'vendor/autoload.php';

use Jane\Component\JsonSchema\Console\Loader\SchemaLoader;
use Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use Jane\Component\JsonSchema\Jane;
use Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use Jane\Component\JsonSchema\Registry\Registry;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpNamespace;

$schemaPath = __DIR__ . '/schema.json';
$targetNamespace = 'WordPress\\Blueprints\\Generated';
$targetDirectory = __DIR__ . '/Generated';

$jane = Jane::build( [
	'reference'  => '#',
	'validation' => true,
	'strict'     => false,
] );
$registry = new Registry();
$schemaLoader = new SchemaLoader();
$registry->addSchema( $schemaLoader->resolve( $schemaPath, [
	'namespace'  => $targetNamespace,
	'directory'  => $targetDirectory,
	'root-class' => 'Blueprint',
	'strict'     => false,
] ) );
$context = $jane->createContext( $registry );
$jane->generate( $registry );

if ( ! file_exists( $targetDirectory ) ) {
	mkdir( $targetDirectory, 0777, true );
}

$schema = $context->getRegistry()->getSchemas()[0];
$janeClasses = $schema->getClasses();

// Every group of classes with a discriminator and oneOf should have
// an interface that all the classes in the group implement.
$netteInterfaces = [];
$shouldImplement = [];
$interfaceImplementors = [];
foreach ( $janeClasses as $ref => $class ) {
	// If $ref ends with /([^/]+)/oneOf/\d, extract the first group
	if ( 1 !== preg_match( '/\/([^\/]+)\/oneOf\/\d+$/', $ref, $matches ) ) {
		continue;
	}

	$interfaceName = $matches[1] . 'Interface';
	$shouldImplement[ $class->getName() ] = $interfaceName;
	$interfaceImplementors[ $interfaceName ][] = $class->getName();

	if ( in_array( $interfaceName, $netteInterfaces ) ) {
		continue;
	}

	$namespace = new PhpNamespace( $targetNamespace );
	$interface = $namespace->addInterface( $interfaceName );
	$netteInterfaces[ $interface->getName() ] = $interface;
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
$unionReplacements = [];
foreach ( $interfaceImplementors as $interfaceName => $implementors ) {
	$unionReplacements[ implode( '|', $implementors ) ] = $interfaceName;

	$arrayUnion = array_map( fn( $implementor ) => $implementor . '[]', $implementors );
	$unionReplacements[ implode( '|', $arrayUnion ) ] = $interfaceName . '[]';
}

/**
 * Jane did the heavy lifting of parsing the JSON Schema and inferring
 * the PHP classes. However, the classes it generates are too opinionated.
 * Let's convert them to Nette classes and customize them to our needs.
 */
$netteClasses = [];
/** @var ClassType[] $netteClasses */
foreach ( $janeClasses as $ref => $janeClass ) {
	$namespace = new PhpNamespace( $targetNamespace );
	$class = $namespace->addClass( $janeClass->getName() );
	if ( isset( $shouldImplement[ $janeClass->getName() ] ) ) {
		$class->addImplement( $targetNamespace . '\\' . $shouldImplement[ $janeClass->getName() ] );
	}
	foreach ( $janeClass->getProperties() as $janeProperty ) {
		$property = $class->addProperty( $janeProperty->getName() );
		$description = $janeProperty->getDescription();
		$docTypeHint = $janeProperty->getType()->getDocTypeHint( '' );
		if ( str_contains( $docTypeHint, $targetNamespace ) ) {
			// Jane prepends "\$namespace\Model\" to type hints, let's remove that.
			$docTypeHint = str_replace( '\\' . $targetNamespace . '\\Model\\', '', $docTypeHint );
			// Let's replace the lengthy union types with the interface types.
			foreach ( $unionReplacements as $union => $replacement ) {
				$docTypeHint = str_replace( $union, $replacement, $docTypeHint );
			}
		}
		if ( $description ) {
			$property->addComment( $description );
		}
		$property->addComment( '@var ' . $docTypeHint . '' );

		$schema = $janeProperty->getObject();
		if ( $schema instanceof JsonSchema ) {
			if ( $schema->getConst() ) {
				$property->setValue( $schema->getConst() );
			} elseif ( $schema->getDefault() ) {
				$property->setValue( $schema->getDefault() );
			}
		}
	}
	$netteClasses[ $class->getName() ] = $class;
}

/**
 * We're finished, yay! Let's write the generated classes to the disk.
 */
$netteOutput = array_merge( $netteInterfaces, $netteClasses );
foreach ( glob( $targetDirectory . '/*.php' ) as $file ) {
	unlink( $file );
}
/** @var ClassType[]|InterfaceType[] $netteOutput */
foreach ( $netteOutput as $entityName => $entity ) {
	$filename = __DIR__ . '/Generated/' . $entityName . '.php';
	file_put_contents( $filename, "<?php\n\n" . $entity->getNamespace() );
}

