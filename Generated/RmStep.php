<?php

namespace WordPress\Blueprints\Generated;

class RmStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'rm';

	/**
	 * The path to remove
	 * @var string
	 */
	public $path;
}
