<?php

namespace WordPress\Blueprints\Generated;

class SetSiteOptionsStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The name of the step. Must be "setSiteOptions".
	 * @var string
	 */
	public $step = 'setSiteOptions';

	/**
	 * The options to set on the site.
	 * @var array<string, mixed>
	 */
	public $options;
}
