<?php

namespace WordPress\Blueprints\Generated\Model;

class CoreThemeResource
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
     * Identifies the file resource as a WordPress Core theme
     *
     * @var string
     */
    protected $resource;
    /**
     * The slug of the WordPress Core theme
     *
     * @var string
     */
    protected $slug;
    /**
     * Identifies the file resource as a WordPress Core theme
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource;
    }
    /**
     * Identifies the file resource as a WordPress Core theme
     *
     * @param string $resource
     *
     * @return self
     */
    public function setResource(string $resource) : self
    {
        $this->initialized['resource'] = true;
        $this->resource = $resource;
        return $this;
    }
    /**
     * The slug of the WordPress Core theme
     *
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }
    /**
     * The slug of the WordPress Core theme
     *
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug) : self
    {
        $this->initialized['slug'] = true;
        $this->slug = $slug;
        return $this;
    }
}