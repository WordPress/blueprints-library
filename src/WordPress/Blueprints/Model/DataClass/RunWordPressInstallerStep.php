<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunWordPressInstallerStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'runWpInstallationWizard';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'runWpInstallationWizard';

	/** @var WordPressInstallationOptions */
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


	/**
	 * @param \WordPress\Blueprints\Model\DataClass\WordPressInstallationOptions $options
	 */
	public function setOptions( $options ) {
		$this->options = $options;
		return $this;
	}
}
