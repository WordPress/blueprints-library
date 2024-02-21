<?php

namespace WordPress\Blueprints\Generated\Model;

class InstallThemeStep
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
     * 
     *
     * @var Progress
     */
    protected $progress;
    /**
     * 
     *
     * @var bool
     */
    protected $continueOnError;
    /**
     * The step identifier.
     *
     * @var string
     */
    protected $step;
    /**
     * 
     *
     * @var string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource
     */
    protected $themeZipFile;
    /**
     * Optional installation options.
     *
     * @var InstallThemeStepOptions
     */
    protected $options;
    /**
     * 
     *
     * @return Progress
     */
    public function getProgress() : Progress
    {
        return $this->progress;
    }
    /**
     * 
     *
     * @param Progress $progress
     *
     * @return self
     */
    public function setProgress(Progress $progress) : self
    {
        $this->initialized['progress'] = true;
        $this->progress = $progress;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getContinueOnError() : bool
    {
        return $this->continueOnError;
    }
    /**
     * 
     *
     * @param bool $continueOnError
     *
     * @return self
     */
    public function setContinueOnError(bool $continueOnError) : self
    {
        $this->initialized['continueOnError'] = true;
        $this->continueOnError = $continueOnError;
        return $this;
    }
    /**
     * The step identifier.
     *
     * @return string
     */
    public function getStep() : string
    {
        return $this->step;
    }
    /**
     * The step identifier.
     *
     * @param string $step
     *
     * @return self
     */
    public function setStep(string $step) : self
    {
        $this->initialized['step'] = true;
        $this->step = $step;
        return $this;
    }
    /**
     * 
     *
     * @return string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource
     */
    public function getThemeZipFile()
    {
        return $this->themeZipFile;
    }
    /**
     * 
     *
     * @param string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource $themeZipFile
     *
     * @return self
     */
    public function setThemeZipFile($themeZipFile) : self
    {
        $this->initialized['themeZipFile'] = true;
        $this->themeZipFile = $themeZipFile;
        return $this;
    }
    /**
     * Optional installation options.
     *
     * @return InstallThemeStepOptions
     */
    public function getOptions() : InstallThemeStepOptions
    {
        return $this->options;
    }
    /**
     * Optional installation options.
     *
     * @param InstallThemeStepOptions $options
     *
     * @return self
     */
    public function setOptions(InstallThemeStepOptions $options) : self
    {
        $this->initialized['options'] = true;
        $this->options = $options;
        return $this;
    }
}