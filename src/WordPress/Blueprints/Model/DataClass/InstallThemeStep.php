<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallThemeStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'installTheme';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'installTheme';

	/** @var string|ResourceDefinitionInterface */
	public $themeZipFile;

	/**
	 * Whether to activate the theme after installing it.
	 * @var bool
	 */
	public $activate = true;


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


	public function setActivate(bool $activate)
	{
		$this->activate = $activate;
		return $this;
	}
}
