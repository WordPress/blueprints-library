<?php

namespace WordPress\Blueprints\Generated;

class MvStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'mv';

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
