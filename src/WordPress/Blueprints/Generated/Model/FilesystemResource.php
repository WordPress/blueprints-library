<?php

namespace WordPress\Blueprints\Generated\Model;

class FilesystemResource
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
     * Identifies the file resource as Virtual File System (VFS)
     *
     * @var string
     */
    protected $resource;
    /**
     * The path to the file in the VFS
     *
     * @var string
     */
    protected $path;
    /**
     * Identifies the file resource as Virtual File System (VFS)
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource;
    }
    /**
     * Identifies the file resource as Virtual File System (VFS)
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
     * The path to the file in the VFS
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }
    /**
     * The path to the file in the VFS
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path) : self
    {
        $this->initialized['path'] = true;
        $this->path = $path;
        return $this;
    }
}