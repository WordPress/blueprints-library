<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunSQLStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'runSql';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'runSql';

	/** @var string|ResourceDefinitionInterface */
	public $sql;


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


	public function setSql( $sql ) {
		$this->sql = $sql;
		return $this;
	}
}
