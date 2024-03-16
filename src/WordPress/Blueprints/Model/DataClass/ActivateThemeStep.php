<?php

namespace WordPress\Blueprints\Model\DataClass;

class ActivateThemeStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'activateTheme';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'activateTheme';

	/**
	 * Theme slug, like 'twentytwentythree'.
	 * @var string
	 */
	public $slug = null;


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


	public function setSlug(string $slug)
	{
		$this->slug = $slug;
		return $this;
	}
}
