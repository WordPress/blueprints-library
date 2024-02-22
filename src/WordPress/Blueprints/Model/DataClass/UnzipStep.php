<?php

namespace WordPress\Blueprints\Model\DataClass;

class UnzipStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'unzip';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'unzip';

	/** @var string|ResourceDefinitionInterface */
	public $zipFile;

	/**
	 * The path of the zip file to extract
	 * @var string
	 */
	public $zipPath;

	/**
	 * The path to extract the zip file to
	 * @var string
	 */
	public $extractToPath;


	public function setProgress(Progress $progress)
	{
		$this->progress = $progress;
		return $this;
	}


	public function setContinueOnError(bool $continueOnError)
	{
		$this->continueOnError = $continueOnError;
		return $this;
	}


	public function setStep(string $step)
	{
		$this->step = $step;
		return $this;
	}


	public function setZipFile($zipFile)
	{
		$this->zipFile = $zipFile;
		return $this;
	}


	public function setZipPath(string $zipPath)
	{
		$this->zipPath = $zipPath;
		return $this;
	}


	public function setExtractToPath(string $extractToPath)
	{
		$this->extractToPath = $extractToPath;
		return $this;
	}
}
