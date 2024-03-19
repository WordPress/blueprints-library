<?php

namespace WordPress\Blueprints\Model\DataClass;

class DefineSiteUrlStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'defineSiteUrl';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'defineSiteUrl';

	/**
	 * The URL
	 *
	 * @var string
	 */
	public $siteUrl;


	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Progress $progress
	 */
	public function setProgress( $progress ) {
		$this->progress = $progress;
		return $this;
	}


	/**
	 * @param bool $continueOnError
	 */
	public function setContinueOnError( $continueOnError ) {
		$this->continueOnError = $continueOnError;
		return $this;
	}


	/**
	 * @param string $step
	 */
	public function setStep( $step ) {
		$this->step = $step;
		return $this;
	}


	/**
	 * @param string $siteUrl
	 */
	public function setSiteUrl( $siteUrl ) {
		$this->siteUrl = $siteUrl;
		return $this;
	}
}
