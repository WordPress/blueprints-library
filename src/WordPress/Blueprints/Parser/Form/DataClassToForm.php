<?php

namespace WordPress\Blueprints\Parser\Form;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use WordPress\Blueprints\Parser\Blueprint;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedCollection;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedDataClassRegistry;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedClass;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedObject;
use WordPress\Blueprints\Resources\URLResource;

class DataClassToForm extends AbstractType {

	public function __construct(
		private DiscriminatedDataClassRegistry $registry,
		private Reader $reader
	) {
	}

	public function buildFormForDataClass( FormBuilderInterface $builder ) {
		// Iterate over properties of $dataClass
		$reflectionClass = new \ReflectionClass( $builder->getDataClass() );
		foreach ( $reflectionClass->getProperties() as $property ) {
			$name = $property->getName();
			foreach ( $this->reader->getPropertyAnnotations( $property ) as $annotation ) {
				if ( $annotation instanceof DiscriminatedObject ) {
					$this->addDiscriminatedObjectField( $builder, $name, $annotation );
					continue 2;
				} elseif ( $annotation instanceof DiscriminatedCollection ) {
					$this->addDiscriminatedCollectionField( $builder, $name, $annotation );
					continue 2;
				}
			}
			$builder->add( $name, TextType::class );
		}

		return $builder->getForm();
	}

	public function addDiscriminatedCollectionField(
		FormBuilderInterface $builder,
		string $name,
		DiscriminatedCollection $annotation
	) {
		$builder
			->add( $name, CollectionType::class, [
				'entry_type'   => TextType::class,
				'allow_add'    => true,
				'allow_delete' => true,
				'by_reference' => false,
			] );

		$builder->addEventListener( FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $builder, $name, $annotation ) {
				$data = $event->getData();
				if ( isset( $data[ $name ] ) ) {
					foreach ( $data[ $name ] as $key => $stepData ) {
						$dataClass = $this->registry->getDataClass( $annotation->group, $stepData[ $annotation->typeProperty ] ?? '' );
						if ( ! $dataClass ) {
							$event->getForm()->get( $name )->addError(
								new FormError(
									'Invalid ' . $annotation->typeProperty . ' type "' . $stepData[ $annotation->typeProperty ] . '"'
								)
							);
							continue;
						}

						$event->getForm()->get( $name )->add(
							$this
								->buildFormForDataClass( $builder->create( $key, FormType::class, [
									'auto_initialize' => 0,
									'data_class'      => $dataClass,
								] ) )
								->add( $annotation->typeProperty, TextType::class, [
									'mapped' => false,
								] )
						);
					}
				}
			} );
	}

	public function addDiscriminatedObjectField(
		FormBuilderInterface $builder,
		string $name,
		DiscriminatedObject $annotation
	) {
		$builder->addEventListener(
			FormEvents::PRE_SUBMIT,
			function ( FormEvent $event ) use ( $builder, $name, $annotation ) {
				$data       = $event->getData();
				$objectData = $data[ $name ] ?? [];
				$dataClass  = $this->registry->getDataClass( $annotation->group, $objectData[ $annotation->typeProperty ] ?? '' );
				if ( ! $dataClass ) {
					$event->getForm()
					      ->add( $name, TextType::class, [
						      'mapped' => false,
					      ] )
					      ->addError(
						      new FormError(
							      'Invalid ' . $annotation->group . ' type "' . $objectData[ $annotation->typeProperty ] . '"'
						      )
					      );

					return;
				}

				$event->getForm()->add(
					$this
						->buildFormForDataClass(
							$builder->create( $name, FormType::class, [
								'auto_initialize' => 0,
								'data_class'      => $dataClass,
							] )
						)
						->add( $annotation->typeProperty, TextType::class, [
							'mapped' => false,
						] )
				);
			}
		);
	}

}

