<?php

namespace WordPress\Blueprints\Generated\Model;

class FileInfoData extends \ArrayObject
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
    protected $bYTESPERELEMENT;
    /**
     * 
     *
     * @var FileInfoDataBuffer
     */
    protected $buffer;
    /**
     * 
     *
     * @var float
     */
    protected $byteLength;
    /**
     * 
     *
     * @var float
     */
    protected $byteOffset;
    /**
     * 
     *
     * @var float
     */
    protected $length;
    /**
     * 
     *
     * @return float
     */
    public function getBYTESPERELEMENT() : float
    {
        return $this->bYTESPERELEMENT;
    }
    /**
     * 
     *
     * @param float $bYTESPERELEMENT
     *
     * @return self
     */
    public function setBYTESPERELEMENT(float $bYTESPERELEMENT) : self
    {
        $this->initialized['bYTESPERELEMENT'] = true;
        $this->bYTESPERELEMENT = $bYTESPERELEMENT;
        return $this;
    }
    /**
     * 
     *
     * @return FileInfoDataBuffer
     */
    public function getBuffer() : FileInfoDataBuffer
    {
        return $this->buffer;
    }
    /**
     * 
     *
     * @param FileInfoDataBuffer $buffer
     *
     * @return self
     */
    public function setBuffer(FileInfoDataBuffer $buffer) : self
    {
        $this->initialized['buffer'] = true;
        $this->buffer = $buffer;
        return $this;
    }
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
    /**
     * 
     *
     * @return float
     */
    public function getByteOffset() : float
    {
        return $this->byteOffset;
    }
    /**
     * 
     *
     * @param float $byteOffset
     *
     * @return self
     */
    public function setByteOffset(float $byteOffset) : self
    {
        $this->initialized['byteOffset'] = true;
        $this->byteOffset = $byteOffset;
        return $this;
    }
    /**
     * 
     *
     * @return float
     */
    public function getLength() : float
    {
        return $this->length;
    }
    /**
     * 
     *
     * @param float $length
     *
     * @return self
     */
    public function setLength(float $length) : self
    {
        $this->initialized['length'] = true;
        $this->length = $length;
        return $this;
    }
}