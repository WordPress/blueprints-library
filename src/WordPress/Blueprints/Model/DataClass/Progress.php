<?php

namespace WordPress\Blueprints\Model\DataClass;

class Progress
{
	/** @var float */
	public $weight = null;

	/** @var string */
	public $caption = null;


	public function setWeight(float $weight)
	{
		$this->weight = $weight;
		return $this;
	}


	public function setCaption(string $caption)
	{
		$this->caption = $caption;
		return $this;
	}
}
