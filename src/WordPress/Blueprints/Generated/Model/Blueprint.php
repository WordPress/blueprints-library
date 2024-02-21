<?php

namespace WordPress\Blueprints\Generated\Model;

class Blueprint
{
    /**
     * @var array
     */
    protected $initialized = [];
    public function isInitialized($property) : bool
    {
        return array_key_exists($property, $this->initialized);
    }
    /**
     * The URL to navigate to after the blueprint has been run.
     *
     * @var string
     */
    protected $landingPage;
    /**
     * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
     *
     * @var string
     */
    protected $description;
    /**
     * The preferred PHP and WordPress versions to use.
     *
     * @var BlueprintPreferredVersions
     */
    protected $preferredVersions;
    /**
     * 
     *
     * @var BlueprintFeatures
     */
    protected $features;
    /**
     * PHP Constants to define on every request
     *
     * @deprecated
     *
     * @var array<string, string>
     */
    protected $constants;
    /**
     * WordPress plugins to install and activate
     *
     * @deprecated
     *
     * @var string[]|string[]|FilesystemResource[]|InlineResource[]|CoreThemeResource[]|CorePluginResource[]|UrlResource[]
     */
    protected $plugins;
    /**
     * WordPress site options to define
     *
     * @deprecated
     *
     * @var BlueprintSiteOptions
     */
    protected $siteOptions;
    /**
     * The PHP extensions to use.
     *
     * @var string[]
     */
    protected $phpExtensionBundles;
    /**
     * The steps to run after every other operation in this Blueprint was executed.
     *
     * @var ActivatePluginStep[]|ActivateThemeStep[]|CpStep[]|DefineWpConfigConstsStep[]|DefineSiteUrlStep[]|EnableMultisiteStep[]|ImportFileStep[]|InstallPluginStep[]|InstallThemeStep[]|MkdirStep[]|MvStep[]|RmStep[]|RmDirStep[]|RunPHPStep[]|RunWordPressInstallerStep[]|RunSQLStep[]|SetSiteOptionsStep[]|UnzipStep[]|WriteFileStep[]|WPCLIStep[]|string[]|mixed[]|bool[]|null[]
     */
    protected $steps;
    /**
     * 
     *
     * @var string
     */
    protected $dollarSchema;
    /**
     * The URL to navigate to after the blueprint has been run.
     *
     * @return string
     */
    public function getLandingPage() : string
    {
        return $this->landingPage;
    }
    /**
     * The URL to navigate to after the blueprint has been run.
     *
     * @param string $landingPage
     *
     * @return self
     */
    public function setLandingPage(string $landingPage) : self
    {
        $this->initialized['landingPage'] = true;
        $this->landingPage = $landingPage;
        return $this;
    }
    /**
     * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }
    /**
     * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description) : self
    {
        $this->initialized['description'] = true;
        $this->description = $description;
        return $this;
    }
    /**
     * The preferred PHP and WordPress versions to use.
     *
     * @return BlueprintPreferredVersions
     */
    public function getPreferredVersions() : BlueprintPreferredVersions
    {
        return $this->preferredVersions;
    }
    /**
     * The preferred PHP and WordPress versions to use.
     *
     * @param BlueprintPreferredVersions $preferredVersions
     *
     * @return self
     */
    public function setPreferredVersions(BlueprintPreferredVersions $preferredVersions) : self
    {
        $this->initialized['preferredVersions'] = true;
        $this->preferredVersions = $preferredVersions;
        return $this;
    }
    /**
     * 
     *
     * @return BlueprintFeatures
     */
    public function getFeatures() : BlueprintFeatures
    {
        return $this->features;
    }
    /**
     * 
     *
     * @param BlueprintFeatures $features
     *
     * @return self
     */
    public function setFeatures(BlueprintFeatures $features) : self
    {
        $this->initialized['features'] = true;
        $this->features = $features;
        return $this;
    }
    /**
     * PHP Constants to define on every request
     *
     * @deprecated
     *
     * @return array<string, string>
     */
    public function getConstants() : iterable
    {
        return $this->constants;
    }
    /**
     * PHP Constants to define on every request
     *
     * @param array<string, string> $constants
     *
     * @deprecated
     *
     * @return self
     */
    public function setConstants(iterable $constants) : self
    {
        $this->initialized['constants'] = true;
        $this->constants = $constants;
        return $this;
    }
    /**
     * WordPress plugins to install and activate
     *
     * @deprecated
     *
     * @return string[]|string[]|FilesystemResource[]|InlineResource[]|CoreThemeResource[]|CorePluginResource[]|UrlResource[]
     */
    public function getPlugins() : array
    {
        return $this->plugins;
    }
    /**
     * WordPress plugins to install and activate
     *
     * @param string[]|string[]|FilesystemResource[]|InlineResource[]|CoreThemeResource[]|CorePluginResource[]|UrlResource[] $plugins
     *
     * @deprecated
     *
     * @return self
     */
    public function setPlugins(array $plugins) : self
    {
        $this->initialized['plugins'] = true;
        $this->plugins = $plugins;
        return $this;
    }
    /**
     * WordPress site options to define
     *
     * @deprecated
     *
     * @return BlueprintSiteOptions
     */
    public function getSiteOptions() : BlueprintSiteOptions
    {
        return $this->siteOptions;
    }
    /**
     * WordPress site options to define
     *
     * @param BlueprintSiteOptions $siteOptions
     *
     * @deprecated
     *
     * @return self
     */
    public function setSiteOptions(BlueprintSiteOptions $siteOptions) : self
    {
        $this->initialized['siteOptions'] = true;
        $this->siteOptions = $siteOptions;
        return $this;
    }
    /**
     * The PHP extensions to use.
     *
     * @return string[]
     */
    public function getPhpExtensionBundles() : array
    {
        return $this->phpExtensionBundles;
    }
    /**
     * The PHP extensions to use.
     *
     * @param string[] $phpExtensionBundles
     *
     * @return self
     */
    public function setPhpExtensionBundles(array $phpExtensionBundles) : self
    {
        $this->initialized['phpExtensionBundles'] = true;
        $this->phpExtensionBundles = $phpExtensionBundles;
        return $this;
    }
    /**
     * The steps to run after every other operation in this Blueprint was executed.
     *
     * @return ActivatePluginStep[]|ActivateThemeStep[]|CpStep[]|DefineWpConfigConstsStep[]|DefineSiteUrlStep[]|EnableMultisiteStep[]|ImportFileStep[]|InstallPluginStep[]|InstallThemeStep[]|MkdirStep[]|MvStep[]|RmStep[]|RmDirStep[]|RunPHPStep[]|RunWordPressInstallerStep[]|RunSQLStep[]|SetSiteOptionsStep[]|UnzipStep[]|WriteFileStep[]|WPCLIStep[]|string[]|mixed[]|bool[]|null[]
     */
    public function getSteps() : array
    {
        return $this->steps;
    }
    /**
     * The steps to run after every other operation in this Blueprint was executed.
     *
     * @param ActivatePluginStep[]|ActivateThemeStep[]|CpStep[]|DefineWpConfigConstsStep[]|DefineSiteUrlStep[]|EnableMultisiteStep[]|ImportFileStep[]|InstallPluginStep[]|InstallThemeStep[]|MkdirStep[]|MvStep[]|RmStep[]|RmDirStep[]|RunPHPStep[]|RunWordPressInstallerStep[]|RunSQLStep[]|SetSiteOptionsStep[]|UnzipStep[]|WriteFileStep[]|WPCLIStep[]|string[]|mixed[]|bool[]|null[] $steps
     *
     * @return self
     */
    public function setSteps(array $steps) : self
    {
        $this->initialized['steps'] = true;
        $this->steps = $steps;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getDollarSchema() : string
    {
        return $this->dollarSchema;
    }
    /**
     * 
     *
     * @param string $dollarSchema
     *
     * @return self
     */
    public function setDollarSchema(string $dollarSchema) : self
    {
        $this->initialized['dollarSchema'] = true;
        $this->dollarSchema = $dollarSchema;
        return $this;
    }
}