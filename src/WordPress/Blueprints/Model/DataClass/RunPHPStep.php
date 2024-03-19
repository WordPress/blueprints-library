<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunPHPStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'runPHP';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'runPHP';

	/**
	 * The PHP code to run.
	 *
	 * @var string
	 */
	public $code;


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
	 * @param string $code
	 */
	public function setCode( $code ) {
		$this->code = $code;
		return $this;
	}
}
