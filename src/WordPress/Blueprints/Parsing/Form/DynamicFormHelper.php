<?php

namespace WordPress\Blueprints\Parsing\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WordPress\Blueprints\Parsing\Constraint\BlueprintSteps;
use WordPress\Blueprints\Parsing\Constraint\ResourceStream;

class DynamicFormHelper extends AbstractType {

	public function __construct(
		private ValidatorInterface $validator,
		private array $availableSteps,
		private array $availableResources
	) {
	}

	public function buildFormForDataClass( FormBuilderInterface $builder, $dataClass ) {
		$metadata = $this->validator->getMetadataFor( $dataClass );
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

		return $builder->getForm();
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

	public function addDynamicCollectionField(
		FormBuilderInterface $builder,
		$dynamicCollectionName,
		$discriminator_field,
		$getDataClass
	) {
		$builder
			->add( $dynamicCollectionName, CollectionType::class, [
				'entry_type'   => TextType::class,
				'allow_add'    => true,
				'allow_delete' => true,
				'by_reference' => false,
			] );

		$builder->addEventListener( FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $builder, $dynamicCollectionName, $discriminator_field, $getDataClass ) {
				$data = $event->getData();
				if ( isset( $data[ $dynamicCollectionName ] ) ) {
					foreach ( $data[ $dynamicCollectionName ] as $key => $stepData ) {
						$this->onPreSubmit(
							$builder,
							$event->getForm()->get( $dynamicCollectionName ),
							$data[ $dynamicCollectionName ],
							$key,
							$discriminator_field,
							$getDataClass
						);
					}
				}
			} );
	}

	public function addDynamicObjectField( FormBuilderInterface $formBuilder, $dynamicFieldName, $discriminator_field, $getDataClass ) {
		$formBuilder->addEventListener(
			FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $formBuilder, $dynamicFieldName, $discriminator_field, $getDataClass ) {
				$this->onPreSubmit( $formBuilder, $event->getForm(), $event->getData(), $dynamicFieldName, $discriminator_field,
					$getDataClass );
			}
		);
		$formBuilder->add( $dynamicFieldName, FormType::class, [
			'data_class' => null,
		] );
	}

	public function onPreSubmit( $builder, FormInterface $form, $data, $name, $discriminator_field, $getDataClass ) {
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
		$form->add(
			$this
				->buildFormForDataClass( $builder->create( $name, FormType::class, [
					'auto_initialize' => 0,
					'data_class'      => $dataClass,
				] ), $dataClass )
				->add( $discriminator_field, TextType::class, [
					'mapped' => false,
				] )
		);
	}
}

