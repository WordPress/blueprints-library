<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunPHPStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'runPHP';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'runPHP';

	/**
	 * The PHP code to run.
	 * @var string
	 */
	public $code = null;


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


	public function setCode(string $code)
	{
		$this->code = $code;
		return $this;
	}
}
