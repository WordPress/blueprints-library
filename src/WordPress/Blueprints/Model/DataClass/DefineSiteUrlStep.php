<?php

namespace WordPress\Blueprints\Model\DataClass;

class DefineSiteUrlStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'defineSiteUrl';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'defineSiteUrl';

	/**
	 * The URL
	 * @var string
	 */
	public $siteUrl = null;


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


	public function setSiteUrl(string $siteUrl)
	{
		$this->siteUrl = $siteUrl;
		return $this;
	}
}
