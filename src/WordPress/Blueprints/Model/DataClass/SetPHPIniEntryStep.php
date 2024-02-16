<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class SetPHPIniEntryStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step;

    /** @var string Entry name e.g. "display_errors" */
    public $key;

    /** @var string Entry value as a string e.g. "1" */
    public $value;
}