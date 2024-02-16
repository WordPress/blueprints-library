<?php

namespace WordPress\Blueprints\Parser\Form\Discriminator;

use Doctrine\Common\Annotations\Reader;

class DiscriminatedDataClassRegistry {

	private $groups = [];

	public function __construct(
		private Reader $reader,
	) {
	}

	public function register( string $dataClass ) {
		if ( ! class_exists( $dataClass ) ) {
			throw new \InvalidArgumentException( "Class $dataClass does not exist" );
		}

		$found = false;

		$annotations = $this->reader->getClassAnnotations( new \ReflectionClass( $dataClass ) );
		foreach ( $annotations as $annotation ) {
			if ( $annotation instanceof DiscriminatedClass ) {
				$found = $annotation;
				break;
			}
		}

		if ( ! $found ) {
			throw new \InvalidArgumentException( "Class $dataClass is not a discriminated data class" );
		}

		/** @var $found DiscriminatedClass */
		if ( ! array_key_exists( $found->group, $this->groups ) ) {
			$this->groups[ $found->group ] = [];
		}
		if ( array_key_exists( $found->id, $this->groups[ $found->group ] ) ) {
			throw new \InvalidArgumentException( "Value {$found->id} is already registered for group {$found->group}" );
		}

		$this->groups[ $found->group ][ $found->id ] = $dataClass;
	}

	public function getDataClass( string $group, string $value ) {
		if ( ! array_key_exists( $group, $this->groups ) ) {
			return null;
		}
		if ( ! array_key_exists( $value, $this->groups[ $group ] ) ) {
			return null;
		}

		return $this->groups[ $group ][ $value ];
	}

}

