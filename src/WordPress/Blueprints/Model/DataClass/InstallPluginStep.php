<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallPluginStep implements StepDefinitionInterface {
	public const DISCRIMINATOR = 'installPlugin';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'installPlugin';

	/** @var string|ResourceDefinitionInterface */
	public $pluginZipFile;

	/**
	 * Whether to activate the plugin after installing it.
	 * @var bool
	 */
	public $activate = true;


	public function setProgress( Progress $progress ) {
		$this->progress = $progress;

		return $this;
	}


	public function setContinueOnError( bool $continueOnError ) {
		$this->continueOnError = $continueOnError;

		return $this;
	}


	public function setStep( string $step ) {
		$this->step = $step;

		return $this;
	}


	public function setPluginZipFile( $pluginZipFile ) {
		$this->pluginZipFile = $pluginZipFile;

		return $this;
	}


	public function setActivate( bool $activate ) {
		$this->activate = $activate;

		return $this;
	}
}
