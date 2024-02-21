<?php

namespace WordPress\Blueprints\Generated;

class RunSQLStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'runSql';

	/** @var string|ResourceDefinitionInterface */
	public $sql;
}
