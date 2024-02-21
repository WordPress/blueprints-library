<?php

namespace WordPress\Blueprints\Generated\Model;

class InlineResource
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
     * Identifies the file resource as an inline string
     *
     * @var string
     */
    protected $resource;
    /**
     * The contents of the file
     *
     * @var string
     */
    protected $contents;
    /**
     * Identifies the file resource as an inline string
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource;
    }
    /**
     * Identifies the file resource as an inline string
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
     * The contents of the file
     *
     * @return string
     */
    public function getContents() : string
    {
        return $this->contents;
    }
    /**
     * The contents of the file
     *
     * @param string $contents
     *
     * @return self
     */
    public function setContents(string $contents) : self
    {
        $this->initialized['contents'] = true;
        $this->contents = $contents;
        return $this;
    }
}