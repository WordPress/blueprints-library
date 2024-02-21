<?php

namespace WordPress\Blueprints\Generated;

class WPCLIStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'wp-cli';

	/**
	 * The WP CLI command to run.
	 * @var string[]
	 */
	public $command;
}
