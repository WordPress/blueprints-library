<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class ActivatePluginStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError;

    /** @var string */
    public $step = 'activatePlugin';

    /** @var string Path to the plugin directory as absolute path (/wordpress/wp-content/plugins/plugin-name); or the plugin entry file relative to the plugins directory (plugin-name/plugin-name.php). */
    public $pluginPath;

    /** @var string Optional. Plugin name to display in the progress bar. */
    public $pluginName;
}