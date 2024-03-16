<?php

namespace WordPress\Blueprints\Model\DataClass;

class DefineWpConfigConstsStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'defineWpConfigConsts';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'defineWpConfigConsts';

	/**
	 * The constants to define
	 * @var \ArrayObject
	 */
	public $consts = null;


	public function setProgress(Progress $progress)
	{
		$this->progress = $progress;
		return $this;
	}


	public function setContinueOnError(bool $continueOnError)
	{
		$this->continueOnError = $continueOnError;
		return $this;
	}


	public function setStep(string $step)
	{
		$this->step = $step;
		return $this;
	}


	public function setConsts(iterable $consts)
	{
		$this->consts = $consts;
		return $this;
	}
}
