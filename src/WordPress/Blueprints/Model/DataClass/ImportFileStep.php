<?php

namespace WordPress\Blueprints\Model\DataClass;

class ImportFileStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'importFile';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'importFile';

	/** @var string|ResourceDefinitionInterface */
	public $file;


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


	public function setFile( $file ) {
		$this->file = $file;
		return $this;
	}
}
