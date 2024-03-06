<?php

namespace WordPress\JsonMapper;

use BadFunctionCallException;
use ReflectionClass;
use UnexpectedValueException;
use function class_exists;
use function is_null;

class ObjectWrapper {
	/** @var object? */
	private $object;

	/** @var class-string? */
	private $class_name;

	/** @var ReflectionClass|null */
	private $reflected_object;

	/**
	 * @param object|null       $object
	 * @param class-string|null $class_name
	 */
	public function __construct( $object = null, string $class_name = null ) {
		if ( is_null( $object ) && is_null( $class_name ) ) {
			throw new BadFunctionCallException( 'Either object or className parameter must be provided, both are null' );
		}
		if ( ! is_null( $class_name ) && ! class_exists( $class_name ) ) {
			throw new UnexpectedValueException(
				sprintf(
					'Argument 2 ($className) must be a valid class name, %s given',
					$class_name
				)
			);
		}

		$this->object     = $object;
		$this->class_name = $class_name;
	}

	/** @param object|null $object */
	public function setObject( $object ) {
		$this->object           = $object;
		$this->reflected_object = null;
	}

	/** @return object */
	public function getObject() {
		if ( is_null( $this->object ) ) {
			$constructor = $this->getReflectedObject()->getConstructor();
			if ( is_null( $constructor ) || $constructor->getNumberOfParameters() === 0 ) {
				$this->object = $this->getReflectedObject()->newInstance();
			} else {
				$this->object = $this->getReflectedObject()->newInstanceWithoutConstructor();
			}
		}

		return $this->object;
	}


	/** @return class-string */
	public function getClassName(): string {
		return $this->class_name;
	}

	public function getReflectedObject(): ReflectionClass {
		if ( $this->reflected_object === null ) {
			$objectOrClass          = ! \is_null( $this->object ) ? $this->object : $this->class_name;
			$this->reflected_object = new ReflectionClass( $objectOrClass );
		}

		return $this->reflected_object;
	}

	public function getName(): string {
		return $this->getReflectedObject()->getName();
	}
}
