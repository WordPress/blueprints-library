<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunWordPressInstallerStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'runWpInstallationWizard';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'runWpInstallationWizard';

	/** @var WordPressInstallationOptions */
	public $options;


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


	public function setOptions(WordPressInstallationOptions $options)
	{
		$this->options = $options;
		return $this;
	}
}
