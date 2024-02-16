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


class ImportWordPressFilesStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError;

    /** @var string */
    public $step = 'importWordPressFiles';

    /** @var string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder */
    public $wordPressFilesZip;

    /** @var string The path inside the zip file where the WordPress files are. */
    public $pathInZip;
}