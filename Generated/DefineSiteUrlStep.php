<?php

namespace WordPress\Blueprints\Generated;

class DefineSiteUrlStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'defineSiteUrl';

	/**
	 * The URL
	 * @var string
	 */
	public $siteUrl;
}
