<?php

namespace WordPress\Blueprints\Generated\Model;

class FileInfoDataBuffer
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
    protected $byteLength;
    /**
     * 
     *
     * @return float
     */
    public function getByteLength() : float
    {
        return $this->byteLength;
    }
    /**
     * 
     *
     * @param float $byteLength
     *
     * @return self
     */
    public function setByteLength(float $byteLength) : self
    {
        $this->initialized['byteLength'] = true;
        $this->byteLength = $byteLength;
        return $this;
    }
}