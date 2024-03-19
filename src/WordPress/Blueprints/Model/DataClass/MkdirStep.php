<?php

namespace WordPress\Blueprints\Model\DataClass;

class MkdirStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'mkdir';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'mkdir';

	/**
	 * The path of the directory you want to create
	 * @var string
	 */
	public $path;


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
