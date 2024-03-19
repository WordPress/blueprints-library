<?php

namespace WordPress\Blueprints\Model\DataClass;

class SetSiteOptionsStep implements StepDefinitionInterface {
	const DISCRIMINATOR = 'setSiteOptions';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The name of the step. Must be "setSiteOptions".
	 *
	 * @var string
	 */
	public $step = 'setSiteOptions';

	/**
	 * The options to set on the site.
	 *
	 * @var \ArrayObject
	 */
	public $options;


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


	public function setOptions( $options ) {
		$this->options = $options;

		return $this;
	}
}
