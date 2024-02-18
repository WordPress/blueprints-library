<?php
/**
 * @file AUTOGENERATED FILE – DO NOT CHANGE MANUALLY
 * All your changes will get overridden. See the README for more details.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;
use WordPress\Blueprints\Model\Builder\WordPressInstallationOptionsBuilder;


class RunWordPressInstallerStep implements StepInterface
{
    const SLUG = 'runWpInstallationWizard';

    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError;

    /** @var string */
    public $step = 'runWpInstallationWizard';

    /** @var WordPressInstallationOptionsBuilder */
    public $options;
}