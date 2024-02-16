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


class UnzipStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step = 'unzip';

    /** @var string|VFSReferenceBuilder|LiteralReferenceBuilder|CoreThemeReferenceBuilder|CorePluginReferenceBuilder|UrlReferenceBuilder */
    public $zipFile;

    /** @var string The path of the zip file to extract */
    public $zipPath;

    /** @var string The path to extract the zip file to */
    public $extractToPath;
}