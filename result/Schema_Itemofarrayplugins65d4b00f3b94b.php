<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare( strict_types=1 );


namespace MyApp\Model;


use PHPModelGenerator\Interfaces\JSONModelInterface;

use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarrayplugins65d4b00f3b94b
 * @package MyApp\Model
 *
 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarrayplugins65d4b00f3b94b implements JSONModelInterface {


	/** @var string Identifies the file resource as an inline string */
	protected $resource;

	/** @var string The contents of the file */
	protected $contents;

	/** @var array */
	protected $_rawModelDataInput = [];


	/** @var ErrorRegistryException Collect all validation errors */
	protected $_errorRegistry;


	/**
	 * Schema_Itemofarrayplugins65d4b00f3b94b constructor.
	 *
	 * @param array $rawModelDataInput
	 *
	 * @throws ErrorRegistryException
	 */
	public function __construct( array $rawModelDataInput = [] ) {

		$this->_errorRegistry = new ErrorRegistryException();


		$this->executeBaseValidators( $rawModelDataInput );


		$this->processResource( $rawModelDataInput );


		$this->processContents( $rawModelDataInput );


		if ( count( $this->_errorRegistry->getErrors() ) ) {
			throw $this->_errorRegistry;
		}


		$this->_rawModelDataInput = $rawModelDataInput;


	}


	protected function executeBaseValidators( array &$modelData ): void {
		$value = &$modelData;


		if ( $additionalProperties = ( static function () use ( $modelData ): array {
			$additionalProperties = array_diff( array_keys( $modelData ), array(
				'resource',
				'contents',
			) );


			return $additionalProperties;
		} )() ) {
			$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException( $value ?? null,
				...
				array(
					0 => 'Schema_Itemofarrayplugins65d4b00f3b94b',
					1 => $additionalProperties,
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


	/**
	 * Get the value of resource.
	 *
	 * Identifies the file resource as an inline string
	 *
	 * @return string
	 */
	public function getResource(): string {


		return $this->resource;
	}


	/**
	 * Set the value of resource.
	 *
	 * @param string $resource Identifies the file resource as an inline string
	 *
	 * @return self
	 * @throws ErrorRegistryException
	 *
	 */
	public function setResource(
		string $resource
	): self {
		if ( $this->resource === $resource ) {
			return $this;
		}

		$value = $modelData['resource'] = $resource;


		$this->_errorRegistry = new ErrorRegistryException();


		$value = $this->validateResource( $value, $modelData );


		if ( $this->_errorRegistry->getErrors() ) {
			throw $this->_errorRegistry;
		}


		$this->resource = $value;
		$this->_rawModelDataInput['resource'] = $resource;


		return $this;
	}


	/**
	 * Extract the value, perform validations and set the property resource
	 *
	 * @param array $modelData
	 *
	 * @throws ErrorRegistryException
	 */
	protected function processResource( array $modelData ): void {


		$value = array_key_exists( 'resource', $modelData ) ? $modelData['resource'] : $this->resource;


		$this->resource = $this->validateResource( $value, $modelData );
	}

	/**
	 * Execute all validators for the property resource
	 */
	protected function validateResource( $value, array $modelData ) {


		if ( $value !== 'inline' ) {
			$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Generic\InvalidConstException( $value ?? null,
				...
				array(
					0 => 'resource',
					1 => 'inline',
				) ) );
		}


		return $value;
	}


	/**
	 * Get the value of contents.
	 *
	 * The contents of the file
	 *
	 * @return string
	 */
	public function getContents(): string {


		return $this->contents;
	}


	/**
	 * Set the value of contents.
	 *
	 * @param string $contents The contents of the file
	 *
	 * @return self
	 * @throws ErrorRegistryException
	 *
	 */
	public function setContents(
		string $contents
	): self {
		if ( $this->contents === $contents ) {
			return $this;
		}

		$value = $modelData['contents'] = $contents;


		$this->_errorRegistry = new ErrorRegistryException();


		$value = $this->validateContents( $value, $modelData );


		if ( $this->_errorRegistry->getErrors() ) {
			throw $this->_errorRegistry;
		}


		$this->contents = $value;
		$this->_rawModelDataInput['contents'] = $contents;


		return $this;
	}


	/**
	 * Extract the value, perform validations and set the property contents
	 *
	 * @param array $modelData
	 *
	 * @throws ErrorRegistryException
	 */
	protected function processContents( array $modelData ): void {


		$value = array_key_exists( 'contents', $modelData ) ? $modelData['contents'] : $this->contents;


		$this->contents = $this->validateContents( $value, $modelData );
	}

	/**
	 * Execute all validators for the property contents
	 */
	protected function validateContents( $value, array $modelData ) {


		if ( ! array_key_exists( 'contents', $modelData ) ) {
			$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Object\RequiredValueException( $value ?? null,
				...
				array(
					0 => 'contents',
				) ) );
		}


		if ( ! is_string( $value ) ) {
			$this->_errorRegistry->addError( new \PHPModelGenerator\Exception\Generic\InvalidTypeException( $value ?? null,
				...
				array(
					0 => 'contents',
					1 => 'string',
				) ) );
		}


		return $value;
	}


}

// @codeCoverageIgnoreEnd
