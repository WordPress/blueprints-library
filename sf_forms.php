<?php

require 'vendor/autoload.php';

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use WordPress\Blueprints\Steps\Unzip\UnzipStepInput;

class Blueprint {
	/**
	 * @Assert\NotBlank
	 */
	public $wpVersion;

	/**
	 * @Assert\Valid
	 */
	public $steps = [];
}

class BasePost {

	/**
	 * @Assert\NotBlank
	 */
	public $content;
}

class ContentPost extends BasePost {
	/**
	 * @Assert\NotBlank
	 * @Assert\Length(max=255)
	 */
	public $title;

}


class FilePost extends BasePost {
	/**
	 * @Assert\NotBlank
	 * @Assert\Length(max=255)
	 */
	public $filePath;
}


class BlueprintType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		add_data_class_fields( $builder, $options['data_class'] );
		$builder
			->add( 'steps', CollectionType::class, [
				'entry_type'   => TextType::class,
				'allow_add'    => true,
				'allow_delete' => true,
				'by_reference' => false,
			] );

		$builder->addEventListener( FormEvents::PRE_SUBMIT, function ( FormEvent $event ) {
			$data = $event->getData(); // Array representation of the submitted data
			$form = $event->getForm();

			$step_types = [
				'unzip' => UnzipStepInput::class,
			];

			if ( isset( $data['steps'] ) ) {
				foreach ( $data['steps'] as $key => $postData ) {
					// Determine the type of post based on postData, e.g., by checking if certain keys exist
					if ( array_key_exists( $postData['step'], $step_types ) ) {
						$dataClass = $step_types[ $postData['step'] ];
					} else {
						$form->addError( new FormError( 'Invalid step type "' . $postData['step'] . '"' ) );
						continue; // Or handle unexpected post type
					}

					// Dynamically adjust the form field for this post
					$form->get( 'steps' )->add( $key, DynamicFormType::class, [
						'data_class' => $dataClass,
					] );
				}
			}
		} );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => Blueprint::class,
		] );
	}
}


class DynamicFormType extends AbstractType {

	public function buildForm( FormBuilderInterface $builder, array $options ) {
		add_data_class_fields( $builder, $options['data_class'] );
		$builder->add( 'step', TextType::class, [
			'mapped' => false,
		] );
	}
}

function add_data_class_fields( FormBuilderInterface $builder, $dataClass ) {
	global $validator;
	$metadata = $validator->getMetadataFor( $dataClass );
	foreach ( $metadata->properties as $property => $value ) {
		$builder->add( $property, TextType::class );
	}
}

// Simulate receiving JSON payload

$validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();


$formBuilder = Forms::createFormFactoryBuilder()
                    ->addExtension( new ValidatorExtension( $validator ) );
$formFactory = $formBuilder->getFormFactory();

$form = $formFactory->create( BlueprintType::class, new Blueprint() );
$form->submit( [
	"wpVersion" => "6.4",
	"steps"     => [
		[ "step" => "unzip", "zipFile" => "test.zip", "toPath" => "test" ],
	],
] );


function get_error_path( Symfony\Component\Form\FormError $error ) {
	$origin = $error->getOrigin();
	$path   = [];
	while ( $origin ) {
		$path[] = $origin->getName();
		$origin = $origin->getParent();
	}

	return implode( '.', array_reverse( $path ) );
}

if ( $form->isSubmitted() && $form->isValid() ) {
	$user = $form->getData();
	// Handle your $user object, e.g., save to database
	print_r( $user );
} else {
	// Display errors
	$errors = [];
	foreach ( $form->getErrors( true, true ) as $error ) {
		/** @var $error Symfony\Component\Form\FormError */
		$errors[ get_error_path( $error ) ] = $error->getMessage() . " – " . implode( ", ", $error->getMessageParameters() );
	}
	// Return or display $errors
	print_r( $errors );
}

