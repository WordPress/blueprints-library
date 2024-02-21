<?php

namespace WordPress\Blueprints\Generated;

class InstallThemeStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'installTheme';

	/** @var string|ResourceDefinitionInterface */
	public $themeZipFile;

	/**
	 * Optional installation options.
	 * @var InstallThemeStepOptions
	 */
	public $options;
}
