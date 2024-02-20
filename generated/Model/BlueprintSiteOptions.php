<?php

namespace WordPress\Blueprints\Generated\Model;

/**
 *
 * @deprecated
 */
class BlueprintSiteOptions extends \ArrayObject
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
     * The site title
     *
     * @var string
     */
    protected $blogname;
    /**
     * The site title
     *
     * @return string
     */
    public function getBlogname() : string
    {
        return $this->blogname;
    }
    /**
     * The site title
     *
     * @param string $blogname
     *
     * @return self
     */
    public function setBlogname(string $blogname) : self
    {
        $this->initialized['blogname'] = true;
        $this->blogname = $blogname;
        return $this;
    }
}