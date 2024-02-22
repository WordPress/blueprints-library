<?php

namespace WordPress\Blueprints\Model\DataClass;

class RmDirStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'rmdir';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'rmdir';

	/**
	 * The path to remove
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
