<?php

namespace WordPress\Blueprints\Generated;

class DefineWpConfigConstsStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'defineWpConfigConsts';

	/**
	 * The constants to define
	 * @var array<string, mixed>
	 */
	public $consts;
}
