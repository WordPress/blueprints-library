<?php

namespace WordPress\Blueprints\Generated\Model;

class WordPressInstallationOptions
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
     * @var string
     */
    protected $adminUsername;
    /**
     * 
     *
     * @var string
     */
    protected $adminPassword;
    /**
     * 
     *
     * @return string
     */
    public function getAdminUsername() : string
    {
        return $this->adminUsername;
    }
    /**
     * 
     *
     * @param string $adminUsername
     *
     * @return self
     */
    public function setAdminUsername(string $adminUsername) : self
    {
        $this->initialized['adminUsername'] = true;
        $this->adminUsername = $adminUsername;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getAdminPassword() : string
    {
        return $this->adminPassword;
    }
    /**
     * 
     *
     * @param string $adminPassword
     *
     * @return self
     */
    public function setAdminPassword(string $adminPassword) : self
    {
        $this->initialized['adminPassword'] = true;
        $this->adminPassword = $adminPassword;
        return $this;
    }
}