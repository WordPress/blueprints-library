<?php

namespace WordPress\Blueprints\Generated;

class RunWordPressInstallerStep implements StepDefinitionInterface
{
	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'runWpInstallationWizard';

	/** @var WordPressInstallationOptions */
	public $options;
}
