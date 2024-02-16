<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ActivatePluginStepBuilder;
use WordPress\Blueprints\Model\Builder\ActivateThemeStepBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintFeaturesBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintSiteOptionsBuilder;
use WordPress\Blueprints\Model\Builder\CorePluginReferenceBuilder;
use WordPress\Blueprints\Model\Builder\CoreThemeReferenceBuilder;
use WordPress\Blueprints\Model\Builder\CpStepBuilder;
use WordPress\Blueprints\Model\Builder\DefineSiteUrlStepBuilder;
use WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder;
use WordPress\Blueprints\Model\Builder\EnableMultisiteStepBuilder;
use WordPress\Blueprints\Model\Builder\ImportFileStepBuilder;
use WordPress\Blueprints\Model\Builder\ImportWordPressFilesStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallThemeStepBuilder;
use WordPress\Blueprints\Model\Builder\LiteralReferenceBuilder;
use WordPress\Blueprints\Model\Builder\LoginDetailsBuilder;
use WordPress\Blueprints\Model\Builder\LoginStepBuilder;
use WordPress\Blueprints\Model\Builder\MkdirStepBuilder;
use WordPress\Blueprints\Model\Builder\MvStepBuilder;
use WordPress\Blueprints\Model\Builder\PHPRequestStepBuilder;
use WordPress\Blueprints\Model\Builder\RmDirStepBuilder;
use WordPress\Blueprints\Model\Builder\RmStepBuilder;
use WordPress\Blueprints\Model\Builder\RunPHPStepBuilder;
use WordPress\Blueprints\Model\Builder\RunPHPWithOptionsStepBuilder;
use WordPress\Blueprints\Model\Builder\RunSQLStepBuilder;
use WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder;
use WordPress\Blueprints\Model\Builder\SetPHPIniEntryStepBuilder;
use WordPress\Blueprints\Model\Builder\SetSiteOptionsStepBuilder;
use WordPress\Blueprints\Model\Builder\UnzipStepBuilder;
use WordPress\Blueprints\Model\Builder\UpdateUserMetaStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlReferenceBuilder;
use WordPress\Blueprints\Model\Builder\VFSReferenceBuilder;
use WordPress\Blueprints\Model\Builder\WPCLIStepBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;


class Blueprint
{
    /** @var string The URL to navigate to after the blueprint has been run. */
    public $landingPage;

    /** @var string Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what. */
    public $description;

    /** @var BlueprintPreferredVersionsBuilder The preferred PHP and WordPress versions to use. */
    public $preferredVersions;

    /** @var BlueprintFeaturesBuilder */
    public $features;

    /** @var string[] PHP Constants to define on every request */
    public $constants;

    /** @var string[]|VFSReferenceBuilder[]|LiteralReferenceBuilder[]|CoreThemeReferenceBuilder[]|CorePluginReferenceBuilder[]|UrlReferenceBuilder[]|array WordPress plugins to install and activate */
    public $plugins;

    /** @var BlueprintSiteOptionsBuilder|string[] WordPress site options to define */
    public $siteOptions;

    /** @var bool|LoginDetailsBuilder User to log in as. If true, logs the user in as admin/password. */
    public $login;

    /** @var string[]|array The PHP extensions to use. */
    public $phpExtensionBundles;

    /** @var ActivatePluginStepBuilder[]|ActivateThemeStepBuilder[]|CpStepBuilder[]|DefineWpConfigConstsStepBuilder[]|DefineSiteUrlStepBuilder[]|EnableMultisiteStepBuilder[]|ImportFileStepBuilder[]|ImportWordPressFilesStepBuilder[]|InstallPluginStepBuilder[]|InstallThemeStepBuilder[]|LoginStepBuilder[]|MkdirStepBuilder[]|MvStepBuilder[]|PHPRequestStepBuilder[]|RmStepBuilder[]|RmDirStepBuilder[]|RunPHPStepBuilder[]|RunPHPWithOptionsStepBuilder[]|RunWordPressInstallerStepBuilder[]|RunSQLStepBuilder[]|SetPHPIniEntryStepBuilder[]|SetSiteOptionsStepBuilder[]|UnzipStepBuilder[]|UpdateUserMetaStepBuilder[]|WriteFileStepBuilder[]|WPCLIStepBuilder[]|string[]|mixed[]|bool[]|null[]|array The steps to run after every other operation in this Blueprint was executed. */
    public $steps;

    /** @var string */
    public $schema;
}