<?php

namespace WordPress\Blueprints\Model\DataClass;

class DefineSiteUrlStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'defineSiteUrl';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'defineSiteUrl';

	/**
	 * The URL
	 * @var string
	 */
	public $siteUrl;


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
