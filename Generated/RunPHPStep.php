<?php

namespace WordPress\Blueprints\Generated;

class RunPHPStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'runPHP';

	/**
	 * The PHP code to run.
	 * @var string
	 */
	public $code;
}
