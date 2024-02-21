<?php

namespace WordPress\Blueprints\Generated;

class InstallPluginStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'installPlugin';

	/** @var string|ResourceDefinitionInterface */
	public $pluginZipFile;

	/** @var InstallPluginOptions */
	public $options;
}
