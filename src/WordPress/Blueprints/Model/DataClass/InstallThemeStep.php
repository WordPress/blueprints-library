<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallThemeStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'installTheme';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'installTheme';

	/** @var string|ResourceDefinitionInterface */
	public $themeZipFile;

	/**
	 * Optional installation options.
	 * @var InstallThemeStepOptions
	 */
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


	public function setThemeZipFile($themeZipFile)
	{
		$this->themeZipFile = $themeZipFile;
		return $this;
	}


	public function setOptions(InstallThemeStepOptions $options)
	{
		$this->options = $options;
		return $this;
	}
}
