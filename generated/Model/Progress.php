<?php

namespace WordPress\Blueprints\Generated\Model;

class Progress
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
     * @var float
     */
    protected $weight;
    /**
     * 
     *
     * @var string
     */
    protected $caption;
    /**
     * 
     *
     * @return float
     */
    public function getWeight() : float
    {
        return $this->weight;
    }
    /**
     * 
     *
     * @param float $weight
     *
     * @return self
     */
    public function setWeight(float $weight) : self
    {
        $this->initialized['weight'] = true;
        $this->weight = $weight;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getCaption() : string
    {
        return $this->caption;
    }
    /**
     * 
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