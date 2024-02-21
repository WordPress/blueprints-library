<?php

namespace WordPress\Blueprints\Generated\Model;

class WriteFileStep
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
     * The path of the file to write to
     *
     * @var string
     */
    protected $path;
    /**
     * The data to write
     *
     * @var string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource|string
     */
    protected $data;
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
     * The path of the file to write to
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }
    /**
     * The path of the file to write to
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path) : self
    {
        $this->initialized['path'] = true;
        $this->path = $path;
        return $this;
    }
    /**
     * The data to write
     *
     * @return string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource|string
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * The data to write
     *
     * @param string|FilesystemResource|InlineResource|CoreThemeResource|CorePluginResource|UrlResource|string $data
     *
     * @return self
     */
    public function setData($data) : self
    {
        $this->initialized['data'] = true;
        $this->data = $data;
        return $this;
    }
}