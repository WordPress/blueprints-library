<?php

namespace WordPress\Blueprints\Model\DataClass;

class ActivateThemeStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'activateTheme';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'activateTheme';

	/**
	 * Theme slug, like 'twentytwentythree'.
	 *
	 * @var string
	 */
	public $slug;


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
	 * @param string $slug
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}
}
