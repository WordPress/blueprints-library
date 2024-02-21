<?php

namespace WordPress\Blueprints\Generated;

class RmDirStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'rmdir';

	/**
	 * The path to remove
	 * @var string
	 */
	public $path;
}
