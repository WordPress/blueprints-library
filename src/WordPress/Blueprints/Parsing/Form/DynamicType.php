<?php

namespace WordPress\Blueprints\Parsing\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicType extends AbstractType {

	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$options['dynamic_form_helper']->buildFormForDataClass( $builder, $options['data_class'] );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class'          => null,
			'dynamic_form_helper' => null,
		] );
		$resolver->setAllowedTypes( 'dynamic_form_helper', [ DynamicFormHelper::class ] );
	}

}

