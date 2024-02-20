<?php
/**
 * @file AUTOGENERATED FILE – DO NOT CHANGE MANUALLY
 * All your changes will get overridden. See the README for more details.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\CorePluginResourceBuilder;
use WordPress\Blueprints\Model\Builder\CoreThemeResourceBuilder;
use WordPress\Blueprints\Model\Builder\FilesystemResourceBuilder;
use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\Builder\ProgressBuilder;
use WordPress\Blueprints\Model\Builder\UrlResourceBuilder;


class WriteFileStep implements StepInterface
{
    const SLUG = 'writeFile';

    /** @var ProgressBuilder */
    public $progress;

    /** @var bool */
    public $continueOnError = false;

    /** @var string */
    public $step = 'writeFile';

    /** @var string The path of the file to write to */
    public $path;

    /** @var string|FilesystemResourceBuilder|InlineResourceBuilder|CoreThemeResourceBuilder|CorePluginResourceBuilder|UrlResourceBuilder|string The data to write */
    public $data;
}