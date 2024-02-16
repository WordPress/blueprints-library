<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class ActivateThemeStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step;

    /** @var string The name of the theme folder inside wp-content/themes/ */
    public $themeFolderName;
}