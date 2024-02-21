<?php

namespace WordPress\Blueprints\Generated\Model;

class SetSiteOptionsStep
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
     * The name of the step. Must be "setSiteOptions".
     *
     * @var string
     */
    protected $step;
    /**
     * The options to set on the site.
     *
     * @var array<string, mixed>
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
     * The name of the step. Must be "setSiteOptions".
     *
     * @return string
     */
    public function getStep() : string
    {
        return $this->step;
    }
    /**
     * The name of the step. Must be "setSiteOptions".
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
     * The options to set on the site.
     *
     * @return array<string, mixed>
     */
    public function getOptions() : iterable
    {
        return $this->options;
    }
    /**
     * The options to set on the site.
     *
     * @param array<string, mixed> $options
     *
     * @return self
     */
    public function setOptions(iterable $options) : self
    {
        $this->initialized['options'] = true;
        $this->options = $options;
        return $this;
    }
}