<?php

namespace WordPress\Blueprints\Generated;

class ImportFileStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'importFile';

	/** @var string|ResourceDefinitionInterface */
	public $file;
}
