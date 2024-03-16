<?php

namespace WordPress\Blueprints\Model\DataClass;

class RmStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'rm';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'rm';

	/**
	 * The path to remove
	 * @var string
	 */
	public $path = null;


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


	public function setPath(string $path)
	{
		$this->path = $path;
		return $this;
	}
}
