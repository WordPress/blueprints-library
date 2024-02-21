<?php

namespace WordPress\Blueprints\Generated\Model;

class InstallPluginOptions
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
     * Whether to activate the plugin after installing it.
     *
     * @var bool
     */
    protected $activate;
    /**
     * Whether to activate the plugin after installing it.
     *
     * @return bool
     */
    public function getActivate() : bool
    {
        return $this->activate;
    }
    /**
     * Whether to activate the plugin after installing it.
     *
     * @param bool $activate
     *
     * @return self
     */
    public function setActivate(bool $activate) : self
    {
        $this->initialized['activate'] = true;
        $this->activate = $activate;
        return $this;
    }
}