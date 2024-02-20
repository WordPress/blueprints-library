<?php

namespace WordPress\Blueprints\Generated\Model;

class WPCLIStep
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
     * The WP CLI command to run.
     *
     * @var string[]
     */
    protected $command;
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
     * The WP CLI command to run.
     *
     * @return string[]
     */
    public function getCommand() : array
    {
        return $this->command;
    }
    /**
     * The WP CLI command to run.
     *
     * @param string[] $command
     *
     * @return self
     */
    public function setCommand(array $command) : self
    {
        $this->initialized['command'] = true;
        $this->command = $command;
        return $this;
    }
}