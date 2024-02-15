<?php

namespace WordPress\Blueprints\Parsing\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Validation;
use WordPress\Blueprints\Parsing\Constraint\BlueprintSteps;
use WordPress\Blueprints\Parsing\Constraint\ResourceStream;

class DynamicFormHelper extends AbstractType {

	public function __construct( private array $availableSteps, private array $availableResources ) {
	}

	public function buildFormForDataClass( FormBuilderInterface $builder, $dataClass ) {
		$validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
		$metadata  = $validator->getMetadataFor( $dataClass );
		foreach ( $metadata->properties as $property => $value ) {
			foreach ( $value->constraints as $constraint ) {
				if ( $constraint instanceof ResourceStream ) {
					$this->addResourceStreamField( $builder, $property );
					continue 2;
				} elseif ( $constraint instanceof BlueprintSteps ) {
					$this->addStepsField( $builder, $property );
					continue 2;
				}
			}
			$builder->add( $property, TextType::class );
		}
	}

	public function addResourceStreamField( FormBuilderInterface $builder, $name ) {
		$this->addDynamicObjectField( $builder, $name, 'source', function ( $resourceKey ) {
			if ( array_key_exists( $resourceKey, $this->availableResources ) ) {
				return $this->availableResources[ $resourceKey ]->dataClass;
			}
		} );
	}

	public function addStepsField( FormBuilderInterface $builder, $name ) {
		$this->addDynamicCollectionField( $builder, $name, 'step', function ( $resourceKey ) {
			if ( array_key_exists( $resourceKey, $this->availableSteps ) ) {
				return $this->availableSteps[ $resourceKey ]->inputClass;
			}
		} );
	}

	public function addDynamicCollectionField( FormBuilderInterface $builder, $name, $discriminator_field, $getDataClass ) {
		$builder
			->add( $name, CollectionType::class, [
				'entry_type'   => TextType::class,
				'allow_add'    => true,
				'allow_delete' => true,
				'by_reference' => false,
			] );

		$builder->addEventListener( FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $name, $discriminator_field, $getDataClass ) {
				$data = $event->getData(); // Array representation of the submitted data
				$form = $event->getForm();
				if ( ! isset( $data[ $name ] ) ) {
					return;
				}
				foreach ( $data[ $name ] as $key => $stepData ) {
					// Determine the type of post based on postData, e.g., by checking if certain keys exist
					$discriminator_value = $stepData[ $discriminator_field ];
					$dataClass           = $getDataClass( $discriminator_value );
					if ( ! $dataClass ) {
						// @TODO: This doesn't seem to work
						$form->get( $name )->addError( new FormError( 'Invalid step type "' . $discriminator_value . '"' ) );
						continue;
					}

					// Dynamically adjust the form field for this post
					$form->get( $name )->add( $key, DynamicType::class, [
						'data_class'          => $dataClass,
						'dynamic_form_helper' => $this,
					] )->get( $key )
					     ->add( $discriminator_field, TextType::class, [
						     'mapped' => false,
					     ] );
				}
			} );
	}

	public function addDynamicObjectField( FormBuilderInterface $builder, $name, $discriminator_field, $getDataClass ) {
		$subBuilder = $builder->create( $name, FormType::class, [
			'data_class' => null,
		] );

		$builder->addEventListener( FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $name, $discriminator_field, $getDataClass ) {
				$data = $event->getData(); // Array representation of the submitted data
				$form = $event->getForm();

				if ( ! isset( $data[ $name ] ) ) {
					return;
				}

				// Determine the type of post based on postData, e.g., by checking if certain keys exist
				$field_data          = $data[ $name ];
				$discriminator_value = $field_data[ $discriminator_field ];
				$dataClass           = $getDataClass( $discriminator_value );
				if ( ! $dataClass ) {
					// @TODO: This doesn't seem to work
					$form->get( $name )->addError( new FormError( 'Invalid resource type "' . $discriminator_value . '"' ) );

					return;
				}

				// Dynamically adjust the form field for this post
				$form->add( $name, DynamicType::class, [
					'data_class'          => $dataClass,
					'dynamic_form_helper' => $this,
				] )->get( $name )
				     ->add( $discriminator_field, TextType::class, [
					     'mapped' => false,
				     ] );
			} );
		$builder->add( $subBuilder );
	}

}

