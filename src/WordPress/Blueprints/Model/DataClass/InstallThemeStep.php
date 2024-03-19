<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallThemeStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'installTheme';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'installTheme';

	/** @var string|ResourceDefinitionInterface */
	public $themeZipFile;

	/**
	 * Whether to activate the theme after installing it.
	 *
	 * @var bool
	 */
	public $activate = true;


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


	public function setThemeZipFile( $themeZipFile ) {
		$this->themeZipFile = $themeZipFile;
		return $this;
	}


	/**
	 * @param bool $activate
	 */
	public function setActivate( $activate ) {
		$this->activate = $activate;
		return $this;
	}
}
