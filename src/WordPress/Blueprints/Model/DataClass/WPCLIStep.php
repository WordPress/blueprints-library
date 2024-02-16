<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class WPCLIStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError;

    /** @var string The step identifier. */
    public $step = 'wp-cli';

    /** @var string|string[]|array The WP CLI command to run. */
    public $command;

    /** @var string wp-cli.phar path */
    public $wpCliPath;
}