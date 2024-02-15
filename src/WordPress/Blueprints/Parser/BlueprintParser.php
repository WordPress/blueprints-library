<?php

namespace WordPress\Blueprints\Parser;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\LoaderChain;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WordPress\Blueprints\BaseType;
use WordPress\Blueprints\BoolType;
use WordPress\Blueprints\Dependency\StepMeta;
use WordPress\Blueprints\FloatType;
use WordPress\Blueprints\InstanceType;
use WordPress\Blueprints\IntType;
use WordPress\Blueprints\ObjectType;
use WordPress\Blueprints\Steps\Unzip\UnzipStepInput;
use WordPress\Blueprints\StringType;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory as SerializerClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader as SerializerAnnotationLoader;

class BlueprintParser {

	public function __construct(
		/** @var StepMeta[] */
		private array $availableSteps,
		private ValidatorInterface $validator
	) {
		$builder = Validation::createValidatorBuilder()
		                     ->enableAnnotationMapping();

		$this->validator = $builder
			->getValidator();

		// Get the private $metadataFactory property from the validator
		$encoders    = [ new JsonEncoder () ];
		$normalizers = [
			new ObjectNormalizer( new SerializerClassMetadataFactory(
				new SerializerAnnotationLoader( new AnnotationReader() )
			) ),
		];
		$serializer  = new Serializer( $normalizers, $encoders );
		$input       = [
			'wpVersion' => '5.8',
			'steps'     => [
				[
					'step'    => 'unzip',
					'zipFile' => fopen( 'php://memory', 'r' ), //fopen( 'test.zip', 'r'
					'toPath'  => 123, //'test',
				],
			],
		];

		$allViolations = [];

		$parsedSteps = [];
		foreach ( $input['steps'] as $step ) {
			$stepMeta        = $this->availableSteps[ $step['step'] ];
			$parsedStep      = $serializer->denormalize( $step, $stepMeta->inputClass );
			$violations      = $this->validator->validate( $parsedStep );
			$parsedSteps[]   = $parsedStep;
			$allViolations[] = $violations;
		}
		var_dump( $parsedStep );
		print_r( $parsedSteps );

		$blueprint        = $serializer->denormalize( $input, Blueprint::class );
		$blueprint->steps = $parsedSteps;

		$allViolations[] = $this->validator->validate( $blueprint );
		foreach ( $allViolations as $group ) {
			foreach ( $group as $k => $violation ) {
				var_dump( $violation->getPropertyPath() . ' => ' . $violation->getMessage() );
			}
		}
		die();
//		$serializer = new Serializer( [ $normalizer ] );
//		$blueprint  = $serializer->deserialize( $input, Blueprint::class, '', [
//			AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
//		] );

		die( var_dump( $blueprint ) );

//		$blueprint->wpVersion = 123;
//		var_dump( $this->validator->validate( $blueprint ) );

		$formFactory = Forms::createFormFactoryBuilder()
		                    ->addExtension( new ValidatorExtension( $this->validator ) )
		                    ->getFormFactory();
		$form        = $formFactory->createBuilder( FormType::class, $blueprint, [
			'data_class' => Blueprint::class,
		] )
		                           ->add( 'wpVersion' )
		                           ->getForm();
		$form->submit();
		$violations = $form->getErrors( false );
		foreach ( $violations as $violation ) {
			var_dump( $violation->getMessage() );
		}
		var_dump( $form->isValid() );
		die();
	}

	public function build() {
		$metadata = $this->validator->getMetadataFor( UnzipStepInput::class );
//		var_dump( $metadata );
//		$schema = array();
		$steps = [];
		foreach ( $this->availableSteps as $step ) {
			$steps[ $step->slug ] = $this->validator->getMetadataFor( $step->inputClass );
		}
		$schema['steps'] = $steps;

		return $schema;
	}


}
