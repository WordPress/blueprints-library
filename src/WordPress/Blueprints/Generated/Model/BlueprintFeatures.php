<?php

namespace WordPress\Blueprints\Generated\Model;

class BlueprintFeatures
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
     * Should boot with support for network request via wp_safe_remote_get?
     *
     * @var bool
     */
    protected $networking;
    /**
     * Should boot with support for network request via wp_safe_remote_get?
     *
     * @return bool
     */
    public function getNetworking() : bool
    {
        return $this->networking;
    }
    /**
     * Should boot with support for network request via wp_safe_remote_get?
     *
     * @param bool $networking
     *
     * @return self
     */
    public function setNetworking(bool $networking) : self
    {
        $this->initialized['networking'] = true;
        $this->networking = $networking;
        return $this;
    }
}