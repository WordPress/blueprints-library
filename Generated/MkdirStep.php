<?php

namespace WordPress\Blueprints\Generated;

class MkdirStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'mkdir';

	/**
	 * The path of the directory you want to create
	 * @var string
	 */
	public $path;
}
