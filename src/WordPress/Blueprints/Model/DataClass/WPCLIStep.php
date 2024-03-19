<?php

namespace WordPress\Blueprints\Model\DataClass;

class WPCLIStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'wp-cli';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/**
	 * The step identifier.
	 *
	 * @var string
	 */
	public $step = 'wp-cli';

	/**
	 * The WP CLI command to run.
	 *
	 * @var string[]
	 */
	public $command;


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
	 * @param mixed[] $command
	 */
	public function setCommand( $command ) {
		$this->command = $command;
		return $this;
	}
}
