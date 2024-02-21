<?php

namespace WordPress\Blueprints\Generated;

class ActivatePluginStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'activatePlugin';

	/**
	 * Plugin slug, like 'gutenberg' or 'hello-dolly'.
	 * @var string
	 */
	public $slug;
}
