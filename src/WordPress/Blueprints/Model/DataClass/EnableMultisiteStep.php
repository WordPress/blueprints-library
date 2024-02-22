<?php

namespace WordPress\Blueprints\Model\DataClass;

class EnableMultisiteStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'enableMultisite';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'enableMultisite';


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
}
