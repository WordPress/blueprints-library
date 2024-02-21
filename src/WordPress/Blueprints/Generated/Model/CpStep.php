<?php

namespace WordPress\Blueprints\Generated\Model;

class CpStep
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
     * Source path
     *
     * @var string
     */
    protected $fromPath;
    /**
     * Target path
     *
     * @var string
     */
    protected $toPath;
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
     * Source path
     *
     * @return string
     */
    public function getFromPath() : string
    {
        return $this->fromPath;
    }
    /**
     * Source path
     *
     * @param string $fromPath
     *
     * @return self
     */
    public function setFromPath(string $fromPath) : self
    {
        $this->initialized['fromPath'] = true;
        $this->fromPath = $fromPath;
        return $this;
    }
    /**
     * Target path
     *
     * @return string
     */
    public function getToPath() : string
    {
        return $this->toPath;
    }
    /**
     * Target path
     *
     * @param string $toPath
     *
     * @return self
     */
    public function setToPath(string $toPath) : self
    {
        $this->initialized['toPath'] = true;
        $this->toPath = $toPath;
        return $this;
    }
}