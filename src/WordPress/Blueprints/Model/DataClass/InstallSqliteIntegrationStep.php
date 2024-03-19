<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallSqliteIntegrationStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'installSqliteIntegration';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'installSqliteIntegration';

	/** @var string|ResourceDefinitionInterface */
	public $sqlitePluginZip;


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


	public function setSqlitePluginZip( $sqlitePluginZip ) {
		$this->sqlitePluginZip = $sqlitePluginZip;
		return $this;
	}
}
