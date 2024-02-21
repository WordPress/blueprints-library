<?php

namespace WordPress\Blueprints\Generated;

class UnzipStep implements StepDefinitionInterface
{
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
}
