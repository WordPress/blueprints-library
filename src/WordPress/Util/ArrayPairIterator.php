<?php

namespace WordPress\Util;

class ArrayPairIterator implements \Iterator {
	private $array;
	private $position = 0;

	public function __construct( array $array ) {
		$this->array = $array;
	}

	#[\ReturnTypeWillChange]
	public function current() {
		return $this->array[ $this->position ][1];
	}

	#[\ReturnTypeWillChange]
	public function key() {
		return $this->array[ $this->position ][0];
	}

	#[\ReturnTypeWillChange]
	public function next() {
		++$this->position;
	}

	#[\ReturnTypeWillChange]
	public function rewind() {
		$this->position = 0;
	}

	#[\ReturnTypeWillChange]
	public function valid() {
		return isset( $this->array[ $this->position ] );
	}
}
