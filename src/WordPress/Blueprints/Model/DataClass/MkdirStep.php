<?php

namespace WordPress\Blueprints\Model\DataClass;

class MkdirStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'mkdir';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'mkdir';

	/**
	 * The path of the directory you want to create
	 *
	 * @var string
	 */
	public $path;


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
	 * @param string $path
	 */
	public function setPath( $path ) {
		$this->path = $path;
		return $this;
	}
}
