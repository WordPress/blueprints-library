<?php

namespace WordPress\Blueprints\Model\DataClass;

class UnzipStep implements StepDefinitionInterface {

	const DISCRIMINATOR = 'unzip';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

	/** @var string */
	public $step = 'unzip';

	/** @var string|ResourceDefinitionInterface */
	public $zipFile;

	/**
	 * The path to extract the zip file to
	 *
	 * @var string
	 */
	public $extractToPath;


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


	public function setZipFile( $zipFile ) {
		$this->zipFile = $zipFile;
		return $this;
	}


	/**
	 * @param string $extractToPath
	 */
	public function setExtractToPath( $extractToPath ) {
		$this->extractToPath = $extractToPath;
		return $this;
	}
}
