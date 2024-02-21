<?php

namespace WordPress\Blueprints\Generated;

class CpStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'cp';

	/**
	 * Source path
	 * @var string
	 */
	public $fromPath;

	/**
	 * Target path
	 * @var string
	 */
	public $toPath;
}
