<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare( strict_types=1 );


namespace MyApp\Model;


use PHPModelGenerator\Interfaces\JSONModelInterface;

use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Constants65d4b00f3b4a2
 * @package MyApp\Model
 *
 * PHP Constants to define on every request
 *
 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Constants65d4b00f3b4a2 implements JSONModelInterface {


	/** @var string[] Collect all additional properties provided to the schema */
	private $_additionalProperties = array();

	/** @var array */
	protected $_rawModelDataInput = [];


	/** @var ErrorRegistryException Collect all validation errors */
	protected $_errorRegistry;


	/**
	 * Schema_Constants65d4b00f3b4a2 constructor.
	 *
	 * @param array $rawModelDataInput
	 *
	 * @throws ErrorRegistryException
	 */
	public function __construct( array $rawModelDataInput = [] ) {

		$this->_errorRegistry = new ErrorRegistryException();


		$this->executeBaseValidators( $rawModelDataInput );


		if ( count( $this->_errorRegistry->getErrors() ) ) {
			throw $this->_errorRegistry;
		}


		$this->_rawModelDataInput = $rawModelDataInput;


	}


	protected function executeBaseValidators( array &$modelData ): void {
		$value = &$modelData;


		$properties = $value;
		$invalidProperties = [];

		if ( ( function () use ( $properties, &$invalidProperties ) {

			$originalErrorRegistry = $this->_errorRegistry;


			$rollbackValues = $this->_additionalProperties;


			foreach (
				array_diff( array_keys( $properties ), array() ) as $propertyKey
			) {


				try {
					$value = $properties[ $propertyKey ];


					$this->_errorRegistry = new ErrorRegistryException();


					if ( ! is_string( $value ) ) {
						$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Generic\InvalidTypeException( $value ?? null,
							...
							array(
								0 => 'additional property',
								1 => 'string',
							) ) );
					}


					if ( $this->_errorRegistry->getErrors() ) {
						$invalidProperties[ $propertyKey ] = $this->_errorRegistry->getErrors();
					}


					$this->_additionalProperties[ $propertyKey ] = $value;

				} catch ( \Exception $e ) {
					// collect all errors concerning invalid additional properties
					isset( $invalidProperties[ $propertyKey ] )
						? $invalidProperties[ $propertyKey ][] = $e
						: $invalidProperties[ $propertyKey ] = [ $e ];
				}
			}


			$this->_errorRegistry = $originalErrorRegistry;


			if ( ! empty( $invalidProperties ) ) {
				$this->_additionalProperties = $rollbackValues;
			}


			return ! empty( $invalidProperties );
		} )() ) {
			$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Object\InvalidAdditionalPropertiesException( $value ?? null,
				...
				array(
					0 => 'Schema_Constants65d4b00f3b4a2',
					1 => $invalidProperties,
				) ) );
		}


	}


	/**
	 * Get the raw input used to set up the model
	 *
	 * @return array
	 */
	public function getRawModelDataInput(): array {
		return $this->_rawModelDataInput;
	}


}

// @codeCoverageIgnoreEnd
