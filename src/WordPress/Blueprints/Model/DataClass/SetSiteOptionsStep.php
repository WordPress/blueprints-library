<?php

namespace WordPress\Blueprints\Model\DataClass;

class SetSiteOptionsStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'setSiteOptions';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The name of the step. Must be "setSiteOptions".
	 * @var string
	 */
	public $step = 'setSiteOptions';

	/**
	 * The options to set on the site.
	 * @var \ArrayObject
	 */
	public $options = null;


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


	public function setOptions(iterable $options)
	{
		$this->options = $options;
		return $this;
	}
}
