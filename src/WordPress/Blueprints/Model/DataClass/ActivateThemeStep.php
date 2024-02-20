<?php
/**
 * @file AUTOGENERATED FILE – DO NOT CHANGE MANUALLY
 * All your changes will get overridden. See the README for more details.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class ActivateThemeStep implements StepInterface
{
    const SLUG = 'activateTheme';

    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError = false;

    /** @var string */
    public $step = 'activateTheme';

    /** @var string Theme slug, like 'twentytwentythree'. */
    public $slug;
}