<?php

namespace WordPress\Blueprints\Model\DataClass;

class CpStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'cp';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'cp';

	/**
	 * Source path
	 *
	 * @var string
	 */
	public $fromPath;

	/**
	 * Target path
	 *
	 * @var string
	 */
	public $toPath;


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
	 * @param string $fromPath
	 */
	public function setFromPath( $fromPath ) {
		$this->fromPath = $fromPath;
		return $this;
	}


	/**
	 * @param string $toPath
	 */
	public function setToPath( $toPath ) {
		$this->toPath = $toPath;
		return $this;
	}
}
