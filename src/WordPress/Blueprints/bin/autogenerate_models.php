<?php

use Swaggest\JsonSchema\Schema;

require __DIR__ . '/../../../../vendor/autoload.php';

$blueprintSchema = json_decode( file_get_contents( __DIR__ . '/../schema.json' ) );

$schema = Schema::import( $blueprintSchema );

$targetPath = __DIR__ . '/../';
if ( ! file_exists( $targetPath ) ) {
	mkdir( $targetPath, 0777, true );
}
$dataClassNs = 'WordPress\\Blueprints\\Model\\DataClass';
$builderNs = 'WordPress\\Blueprints\\Model\\Builder';
$runnerNs = 'WordPress\\Blueprints\\Runner\\Step';

/**
 * Same as \Swaggest\PhpCodeBuilder\App\PhpApp. but does not
 * remove everything from the $targetPath directory. We store
 * more than just the autogenerated files in there and we don't
 * want to remove them. This version of the class only clears
 * the directories associated with the namespaces we use for
 * the generated classes.
 */
class CarefulPhpApp extends \Swaggest\PhpCodeBuilder\App\PhpApp {
	protected $psr4Namespaces = [];
	protected $directoriesToClear = [];
	protected $defaultFileComment = "AUTOGENERATED FILE – DO NOT CHANGE MANUALLY\nAll your changes will get overridden. See the README for more details.";


	public function setNamespaceRoot( $namespace, $relativePath = './src/', $clear = false ) {
		$relativePath = rtrim( $relativePath, '/' ) . '/';
		$this->psr4Namespaces[ $namespace ] = $relativePath;
		parent::setNamespaceRoot( $namespace, $relativePath );

		if ( $clear ) {
			$this->directoriesToClear[] = $relativePath;
		}

		return $this;
	}

	public function generatedClassExists( $rootPath, $namespace, $className ) {
		$namespacePath = $this->psr4Namespaces[ $namespace ];
		$rootPath = rtrim( $rootPath, '/' ) . '/';
		$classPath = $rootPath . $namespacePath . $className . '.php';

		return file_exists( $classPath );
	}

	public function addClass( \Swaggest\PhpCodeBuilder\PhpClassTraitInterface $class ) {
		$filesBefore = $this->getFiles();
		parent::addClass( $class );
		$filesAfter = $this->getFiles();
		$diff = array_diff_key( $filesAfter, $filesBefore );

		$file = array_values( $diff )[0];
		$this->setAutogeneratedFileComment( $file, $this->defaultFileComment );

		return $file;
	}

	public function setAutogeneratedFileComment( $file, $comment ) {
		$phpFileRef = new ReflectionClass( Swaggest\PhpCodeBuilder\PhpFile::class );
		$fileCommentProp = $phpFileRef->getProperty( "comment" );
		$fileCommentProp->setValue(
			$file,
			$comment
		);
	}

	public function getFiles() {
		$appRef = new ReflectionClass( parent::class );
		$appFilesProp = $appRef->getProperty( 'files' );

		return $appFilesProp->getValue( $this );
	}


	public function store( $path ) {
		foreach ( $this->directoriesToClear as $relativePath ) {
			$dir = realpath( $path . $relativePath );
			foreach ( glob( $dir . '/*.php' ) as $oldFile ) {
				unlink( $oldFile );
			}
		}

		if ( DIRECTORY_SEPARATOR === '\\' ) {
			$path = str_replace( '\\', '/', $path );
		}

		$path = rtrim( $path, '/' ) . '/';

		foreach ( $this->files as $filepath => $contents ) {
			$this->putContents( $path . $filepath, $contents );
		}
	}

}

$app = new CarefulPhpApp();
$app->setNamespaceRoot( $dataClassNs, './Model/DataClass', $clear = true );
$app->setNamespaceRoot( $builderNs, './Model/Builder', $clear = true );
$app->setNamespaceRoot( $runnerNs, './Runner/Step' );

$builder = new \Swaggest\PhpCodeBuilder\JsonSchema\PhpBuilder();
$builder->buildSetters = true;
$builder->makeEnumConstants = true;
$builder->declarePropertyDefaults = true;

$builder->classCreatedHook = new \Swaggest\PhpCodeBuilder\JsonSchema\ClassHookCallback(
	function ( \Swaggest\PhpCodeBuilder\PhpClass $class, $path, $schema ) use ( $app, $builderNs ) {
		$desc = '';
		if ( $schema->title ) {
			$desc = $schema->title;
		}
		if ( $schema->description ) {
			$desc .= "\n" . $schema->description;
		}
		if ( $fromRefs = $schema->getFromRefs() ) {
			$desc .= "\nBuilt from " . implode( "\n" . ' <- ', $fromRefs );
		}

		$class->setDescription( trim( $desc ) );
		$class->setNamespace( $builderNs );
		if ( '#' === $path ) {
			$class->setName( 'Blueprint' ); // Class name for root schema
		} elseif ( strpos( $path, '#/definitions/' ) === 0 ) {
			$class->setName(
				\Swaggest\PhpCodeBuilder\PhpCode::makePhpClassName( substr( $path,
					strlen( '#/definitions/' ) ) ) . 'Builder'
			);
		}
		$app->addClass( $class );
	}
);

// Make all FileReference and StepDefinition models share a
// common interface.
// I didn't find any way of getting those information from $schema,
// so here's a native extraction of all the relevant classes.
$interfaces = [
	'FileReference'  => [
		'interfaceName' => 'FileReferenceInterface',
	],
	'StepDefinition' => [
		'interfaceName' => 'StepInterface',
	],
];
foreach ( $interfaces as $definitionName => $details ) {
	$schemaDefinition = $blueprintSchema->definitions->{$definitionName};
	$interfaces[ $definitionName ]['implementers'] = [];
	$relatedRefs = property_exists( $schemaDefinition,
		'anyOf' ) ? $schemaDefinition->anyOf : $schemaDefinition->oneOf;
	foreach ( $relatedRefs as $name => $property ) {
		if ( $property->{'$ref'} ) {
			$parts = explode( '/', $property->{'$ref'} );
			$interfaces[ $definitionName ]['implementers'][] = end( $parts );
		}
	}
	$interfaces[ $definitionName ]['interface'] = ( new \Swaggest\PhpCodeBuilder\PhpInterface() )
		->setName( $details['interfaceName'] )
		->setNamespace( $dataClassNs );
	$app->addClass( $interfaces[ $definitionName ]['interface'] );
}

$builder->classPreparedHook = new \Swaggest\PhpCodeBuilder\JsonSchema\ClassHookCallback(
	function ( \Swaggest\PhpCodeBuilder\PhpClass $class, $path, $schema ) use (
		$app,
		$targetPath,
		$builderNs,
		$dataClassNs,
		$runnerNs,
		$interfaces,
	) {
		$dataClass = new \Swaggest\PhpCodeBuilder\PhpClass();
		// Remove the "Builder" suffix from the class name
		$dataClassName = substr( $class->getName(), 0, - 7 );
		$dataClass->setName( $dataClassName );
		$dataClass->setNamespace( $dataClassNs );
		// Add the relevant interfaces, like StepDefinitionInterface
		foreach ( $interfaces as $details ) {
			if ( in_array( $dataClassName, $details['implementers'] ) ) {
				$dataClass->addImplements( $details['interface'] );
			}
		}

		// Add all the properties from the builder class to the data class
		foreach ( $class->getProperties() as $property ) {
			$dataClass->addProperty( $property );
		}

		// ...and remove them from the builder class – they will be inherited
		$ref = new ReflectionObject( $class );
		$prop = $ref->getProperty( 'properties' );
		$prop->setAccessible( true );
		$prop->setValue( $class, [] );

		// Add default values for the "const" properties
		$schemaProperties = $schema->getProperties();
		foreach ( $dataClass->getProperties() as $property ) {
			$name = $property->getNamedVar()->getName();
			if ( $schemaProperties->$name && $schemaProperties->$name->const ) {
				if (
					( str_ends_with( $dataClassName, 'Step' ) && $name === 'step' )
					|| ( str_ends_with( $dataClassName, 'Resource' ) && $name === 'resource' )
				) {
					$const = new \Swaggest\PhpCodeBuilder\PhpConstant( 'SLUG', $schemaProperties->$name->const );
					$dataClass->addConstant( $const );
				}
				$property->getNamedVar()->setDefault( $schemaProperties->$name->const );
			}
		}

		// Add a toDataObject method to the builder class that will return a new instance of the data class
		$toDataObjectBody = "\$dataObject = new $dataClassName();\n";
		foreach ( $dataClass->getProperties() as $property ) {
			$name = $property->getNamedVar()->getName();
			$toDataObjectBody .= "\$dataObject->$name = \$this->recursiveJsonSerialize(\$this->{$name});\n";
		}
		$toDataObjectBody .= 'return $dataObject;';
		$class->addMethod( ( new \Swaggest\PhpCodeBuilder\PhpFunction( 'toDataObject' ) )->setBody( $toDataObjectBody ) );
		$class->addMethod( ( new \Swaggest\PhpCodeBuilder\PhpFunction( 'recursiveJsonSerialize' ) )->addArgument(
			new \Swaggest\PhpCodeBuilder\PhpNamedVar( 'objectMaybe' )
		)->setVisibility( 'private' )->setBody( <<<'METHOD'
if ( is_array( $objectMaybe ) ) {
	return array_map([$this, 'recursiveJsonSerialize'], $objectMaybe);
} elseif ( $objectMaybe instanceof \Swaggest\JsonSchema\Structure\ClassStructureContract ) {
	return $objectMaybe->toDataObject();
} else {
	return $objectMaybe;
}
METHOD
		) );

		// We want to extend the data class so let's replace the default inheritance
		// approach in the schema class with a trait and an interface
		$class->setExtends( $dataClass );
		$class->addTrait( new \Swaggest\PhpCodeBuilder\PhpTrait( '\\Swaggest\\JsonSchema\\Structure\\ClassStructureTrait' ) );
		$class->addImplements( ( new \Swaggest\PhpCodeBuilder\PhpInterface() )->setName( '\\Swaggest\\JsonSchema\\Structure\\ClassStructureContract' ) );

		$app->addClass( $dataClass );

		$runnerClassName = $dataClassName . 'Runner';
		// Create a Step Handler class if it's needed but missing
		if (
			str_ends_with( $dataClassName, 'Step' ) &&
			! $app->generatedClassExists( $targetPath, $runnerNs, $runnerClassName )
		) {
			$baseStepRunnerClass = new \Swaggest\PhpCodeBuilder\PhpClass();
			$baseStepRunnerClass->setNamespace( $runnerNs );
			$baseStepRunnerClass->setName( 'BaseStepRunner' );

			$runnerClass = new \Swaggest\PhpCodeBuilder\PhpClass();
			$runnerClass->setNamespace( $runnerNs );
			$runnerClass->setExtends( $baseStepRunnerClass );
			$runnerClass->setName( $runnerClassName );
			$execute = new \Swaggest\PhpCodeBuilder\PhpFunction( 'run' );
			$execute->setBody( '' );

			$input = new \Swaggest\PhpCodeBuilder\PhpNamedVar( 'input' );
			$input->setType( $dataClass );
			$execute->addArgument( $input );

			$tracker = new \Swaggest\PhpCodeBuilder\PhpNamedVar( 'tracker' );
			$tracker->setType( ( new \Swaggest\PhpCodeBuilder\PhpClass() )->setFullyQualifiedName( 'WordPress\\Blueprints\\Progress\\Tracker' ) );
			$execute->addArgument( $tracker );

			$runnerClass->addMethod( $execute );
			$file = $app->addClass( $runnerClass );
			$app->setAutogeneratedFileComment( $file, '' );
		}
	}
);

$builder->getType( $schema );
$app->store( $targetPath );
