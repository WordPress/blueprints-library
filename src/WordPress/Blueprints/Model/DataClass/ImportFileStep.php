<?php

namespace WordPress\Blueprints\Model\DataClass;

class ImportFileStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'importFile';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'importFile';

	/** @var string|ResourceDefinitionInterface */
	public $file;


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


	public function setFile($file)
	{
		$this->file = $file;
		return $this;
	}
}
