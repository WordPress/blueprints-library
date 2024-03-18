<?php

namespace WordPress\Blueprints\Model\DataClass;

class DownloadWordPressStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'downloadWordPress';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'downloadWordPress';

	/** @var string|ResourceDefinitionInterface */
	public $wordPressZip;


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


	public function setWordPressZip($wordPressZip)
	{
		$this->wordPressZip = $wordPressZip;
		return $this;
	}
}
