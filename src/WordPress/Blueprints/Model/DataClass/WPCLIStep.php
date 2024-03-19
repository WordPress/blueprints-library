<?php

namespace WordPress\Blueprints\Model\DataClass;

class WPCLIStep implements StepDefinitionInterface
{
	const DISCRIMINATOR = 'wp-cli';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError = false;

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


	public function setCommand(array $command)
	{
		$this->command = $command;
		return $this;
	}
}
