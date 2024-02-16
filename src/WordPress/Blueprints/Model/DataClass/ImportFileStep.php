<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\CorePluginReferenceBuilder;
use WordPress\Blueprints\Model\Builder\CoreThemeReferenceBuilder;
use WordPress\Blueprints\Model\Builder\LiteralReferenceBuilder;
use WordPress\Blueprints\Model\Builder\ProgressBuilder;
use WordPress\Blueprints\Model\Builder\UrlReferenceBuilder;
use WordPress\Blueprints\Model\Builder\VFSReferenceBuilder;


class ImportFileStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step = 'importFile';

    /** @var string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder */
    public $file;
}