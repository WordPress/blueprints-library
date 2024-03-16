<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfo
{
	/** @var string */
	public $key = null;

	/** @var string */
	public $name = null;

	/** @var string */
	public $type = null;

	/** @var FileInfoData */
	public $data = null;


	public function setKey(string $key)
	{
		$this->key = $key;
		return $this;
	}


	public function setName(string $name)
	{
		$this->name = $name;
		return $this;
	}


	public function setType(string $type)
	{
		$this->type = $type;
		return $this;
	}


	public function setData(FileInfoData $data)
	{
		$this->data = $data;
		return $this;
	}
}
