<?php

namespace WordPress\Blueprints\Model\DataClass;

class WriteFileStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'writeFile';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'writeFile';

	/**
	 * The path of the file to write to
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The data to write
	 *
	 * @var string|ResourceDefinitionInterface
	 */
	public $data;


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


	public function setData( $data ) {
		$this->data = $data;
		return $this;
	}
}
