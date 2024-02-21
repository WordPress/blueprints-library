<?php

namespace WordPress\Blueprints\Generated;

class ActivateThemeStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'activateTheme';

	/**
	 * Theme slug, like 'twentytwentythree'.
	 * @var string
	 */
	public $slug;
}
