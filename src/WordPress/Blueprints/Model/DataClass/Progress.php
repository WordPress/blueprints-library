<?php

namespace WordPress\Blueprints\Model\DataClass;

class Progress
{
	/** @var float */
	public $weight;

	/** @var string */
	public $caption;


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
