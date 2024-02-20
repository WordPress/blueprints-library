<?php

namespace WordPress\Blueprints\Generated\Model;

class DefineSiteUrlStep
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
     * The URL
     *
     * @var string
     */
    protected $siteUrl;
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
     * The URL
     *
     * @return string
     */
    public function getSiteUrl() : string
    {
        return $this->siteUrl;
    }
    /**
     * The URL
     *
     * @param string $siteUrl
     *
     * @return self
     */
    public function setSiteUrl(string $siteUrl) : self
    {
        $this->initialized['siteUrl'] = true;
        $this->siteUrl = $siteUrl;
        return $this;
    }
}