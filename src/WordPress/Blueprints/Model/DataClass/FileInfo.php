<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfo {

	/** @var string */
	public $key;

	/** @var string */
	public $name;

	/** @var string */
	public $type;

	/** @var FileInfoData */
	public $data;


	/**
	 * @param string $key
	 */
	public function setKey( $key ) {
		$this->key = $key;
		return $this;
	}


	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}


	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;
		return $this;
	}


	/**
	 * @param \WordPress\Blueprints\Model\DataClass\FileInfoData $data
	 */
	public function setData( $data ) {
		$this->data = $data;
		return $this;
	}
}
