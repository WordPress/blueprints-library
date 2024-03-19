<?php

namespace WordPress\Blueprints\Model\DataClass;

class EvalPHPCallbackStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'evalPHPCallback';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'evalPHPCallback';

	/**
	 * The PHP function.
	 *
	 * @var mixed
	 */
	public $callback;


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


	public function setCallback( $callback ) {
		$this->callback = $callback;
		return $this;
	}
}
