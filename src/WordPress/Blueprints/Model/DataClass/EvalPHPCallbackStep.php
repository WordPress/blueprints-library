<?php

namespace WordPress\Blueprints\Model\DataClass;

class EvalPHPCallbackStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'evalPHPCallback';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'evalPHPCallback';

	/**
	 * The PHP function.
	 * @var mixed
	 */
	public $callback;


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


	public function setCallback($callback)
	{
		$this->callback = $callback;
		return $this;
	}
}
