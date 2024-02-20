<?php
/**
 * @file AUTOGENERATED FILE – DO NOT CHANGE MANUALLY
 * All your changes will get overridden. See the README for more details.
 */

namespace WordPress\Blueprints\Model\InternalValidated;

use WordPress\Blueprints\Model\Dirty\CorePluginResource;
use WordPress\Blueprints\Model\Dirty\CoreThemeResource;
use WordPress\Blueprints\Model\Dirty\FilesystemResource;
use WordPress\Blueprints\Model\Dirty\InlineResource;
use WordPress\Blueprints\Model\Dirty\Progress;
use WordPress\Blueprints\Model\Dirty\UrlResource;


class ValidRunSQLStep
{
    const SLUG = 'runSql';

    /** @var Progress */
    public $progress;

    /** @var bool */
    public $continueOnError = false;

    /** @var string The step identifier. */
    public $step = 'runSql';

    /** @var string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource */
    public $sql;

    /**
     * @param string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource $sql
     * @param Progress $progress
     * @param bool $continueOnError
     */
    function __construct($sql, Progress $progress = NULL, $continueOnError = NULL)
    {
        $this->sql = $sql;
        $this->progress = $progress;
        $this->continueOnError = $continueOnError;
    }
}