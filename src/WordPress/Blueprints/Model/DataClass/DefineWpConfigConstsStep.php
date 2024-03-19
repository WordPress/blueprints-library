<?php

namespace WordPress\Blueprints\Model\DataClass;

class DefineWpConfigConstsStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'defineWpConfigConsts';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'defineWpConfigConsts';

	/**
	 * The constants to define
	 *
	 * @var \ArrayObject
	 */
	public $consts;


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
	 * @param iterable $consts
	 */
	public function setConsts( $consts ) {
		$this->consts = $consts;
		return $this;
	}
}
