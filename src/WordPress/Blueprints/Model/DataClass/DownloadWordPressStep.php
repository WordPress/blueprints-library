<?php

namespace WordPress\Blueprints\Model\DataClass;

class DownloadWordPressStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'downloadWordPress';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'downloadWordPress';

	/** @var string|ResourceDefinitionInterface */
	public $wordPressZip;


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


	public function setWordPressZip( $wordPressZip ) {
		$this->wordPressZip = $wordPressZip;
		return $this;
	}
}
