<?php

namespace WordPress\Blueprints\Model\DataClass;

class ActivatePluginStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'activatePlugin';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'activatePlugin';

	/**
	 * Plugin slug, like 'gutenberg' or 'hello-dolly'.
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
