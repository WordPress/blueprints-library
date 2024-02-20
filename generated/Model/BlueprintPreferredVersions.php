<?php

namespace WordPress\Blueprints\Generated\Model;

class BlueprintPreferredVersions
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
     * The preferred PHP version to use. If not specified, the latest supported version will be used
     *
     * @var string
     */
    protected $php;
    /**
     * The preferred WordPress version to use. If not specified, the latest supported version will be used
     *
     * @var string
     */
    protected $wp;
    /**
     * The preferred PHP version to use. If not specified, the latest supported version will be used
     *
     * @return string
     */
    public function getPhp() : string
    {
        return $this->php;
    }
    /**
     * The preferred PHP version to use. If not specified, the latest supported version will be used
     *
     * @param string $php
     *
     * @return self
     */
    public function setPhp(string $php) : self
    {
        $this->initialized['php'] = true;
        $this->php = $php;
        return $this;
    }
    /**
     * The preferred WordPress version to use. If not specified, the latest supported version will be used
     *
     * @return string
     */
    public function getWp() : string
    {
        return $this->wp;
    }
    /**
     * The preferred WordPress version to use. If not specified, the latest supported version will be used
     *
     * @param string $wp
     *
     * @return self
     */
    public function setWp(string $wp) : self
    {
        $this->initialized['wp'] = true;
        $this->wp = $wp;
        return $this;
    }
}