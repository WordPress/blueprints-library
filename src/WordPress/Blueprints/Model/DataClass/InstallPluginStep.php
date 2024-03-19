<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallPluginStep implements StepDefinitionInterface {
	const DISCRIMINATOR = 'installPlugin';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'installPlugin';

	/** @var string|ResourceDefinitionInterface */
	public $pluginZipFile;

	/**
	 * Whether to activate the plugin after installing it.
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


	public function setPluginZipFile( $pluginZipFile ) {
		$this->pluginZipFile = $pluginZipFile;

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
