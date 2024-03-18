<?php

namespace WordPress\Blueprints\Model\DataClass;

class WriteFileStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'writeFile';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'writeFile';

	/**
	 * The path of the file to write to
	 * @var string
	 */
	public $path;

	/**
	 * The data to write
	 * @var string|ResourceDefinitionInterface
	 */
	public $data;


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


	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
}
