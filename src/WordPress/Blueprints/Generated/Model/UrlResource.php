<?php

namespace WordPress\Blueprints\Generated\Model;

class UrlResource
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
     * Identifies the file resource as a URL
     *
     * @var string
     */
    protected $resource;
    /**
     * The URL of the file
     *
     * @var string
     */
    protected $url;
    /**
     * Optional caption for displaying a progress message
     *
     * @var string
     */
    protected $caption;
    /**
     * Identifies the file resource as a URL
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource;
    }
    /**
     * Identifies the file resource as a URL
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
     * The URL of the file
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }
    /**
     * The URL of the file
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url) : self
    {
        $this->initialized['url'] = true;
        $this->url = $url;
        return $this;
    }
    /**
     * Optional caption for displaying a progress message
     *
     * @return string
     */
    public function getCaption() : string
    {
        return $this->caption;
    }
    /**
     * Optional caption for displaying a progress message
     *
     * @param string $caption
     *
     * @return self
     */
    public function setCaption(string $caption) : self
    {
        $this->initialized['caption'] = true;
        $this->caption = $caption;
        return $this;
    }
}