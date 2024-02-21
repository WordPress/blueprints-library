<?php

namespace WordPress\Blueprints\Generated;

class WriteFileStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'writeFile';

	/**
	 * The path of the file to write to
	 * @var string
	 */
	public $path;

	/**
	 * The data to write
	 * @var string|ResourceDefinitionInterface|string
	 */
	public $data;
}
