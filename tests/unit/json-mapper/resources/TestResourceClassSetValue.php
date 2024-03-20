<?php

// phpcs:disable
class TestResourceClassSetValue {
	public $publicProperty;

	protected $protectedProperty;

	protected $setterlessProtectedProperty;

	private $privateProperty;

	private $setterlessPrivateProperty;

	public function setProtectedProperty( $value ) {
		$this->privateProperty = $value;
	}

	public function setPrivateProperty( $value ) {
		$this->privateProperty = $value;
	}
}
