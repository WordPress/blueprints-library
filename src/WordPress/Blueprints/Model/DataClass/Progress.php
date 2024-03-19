<?php

namespace WordPress\Blueprints\Model\DataClass;

class Progress {

	/** @var float */
	public $weight;

	/** @var string */
	public $caption;


	/**
	 * @param float $weight
	 */
	public function setWeight( $weight ) {
		$this->weight = $weight;
		return $this;
	}


	/**
	 * @param string $caption
	 */
	public function setCaption( $caption ) {
		$this->caption = $caption;
		return $this;
	}
}
