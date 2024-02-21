<?php

namespace WordPress\Blueprints\Generated\Model;

class UnzipStep
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
     * 
     *
     * @var string
     */
    protected $step;
    /**
     * 
     *
     * @var string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource
     */
    protected $zipFile;
    /**
     * The path of the zip file to extract
     *
     * @deprecated
     *
     * @var string
     */
    protected $zipPath;
    /**
     * The path to extract the zip file to
     *
     * @var string
     */
    protected $extractToPath;
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
     * 
     *
     * @return string
     */
    public function getStep() : string
    {
        return $this->step;
    }
    /**
     * 
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
    public function getZipFile()
    {
        return $this->zipFile;
    }
    /**
     * 
     *
     * @param string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource $zipFile
     *
     * @return self
     */
    public function setZipFile($zipFile) : self
    {
        $this->initialized['zipFile'] = true;
        $this->zipFile = $zipFile;
        return $this;
    }
    /**
     * The path of the zip file to extract
     *
     * @deprecated
     *
     * @return string
     */
    public function getZipPath() : string
    {
        return $this->zipPath;
    }
    /**
     * The path of the zip file to extract
     *
     * @param string $zipPath
     *
     * @deprecated
     *
     * @return self
     */
    public function setZipPath(string $zipPath) : self
    {
        $this->initialized['zipPath'] = true;
        $this->zipPath = $zipPath;
        return $this;
    }
    /**
     * The path to extract the zip file to
     *
     * @return string
     */
    public function getExtractToPath() : string
    {
        return $this->extractToPath;
    }
    /**
     * The path to extract the zip file to
     *
     * @param string $extractToPath
     *
     * @return self
     */
    public function setExtractToPath(string $extractToPath) : self
    {
        $this->initialized['extractToPath'] = true;
        $this->extractToPath = $extractToPath;
        return $this;
    }
}