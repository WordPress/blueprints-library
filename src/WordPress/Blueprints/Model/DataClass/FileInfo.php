<?php

namespace WordPress\Blueprints\Model\DataClass;

class FileInfo
{
	/** @var string */
	public $key;

	/** @var string */
	public $name;

	/** @var string */
	public $type;

	/** @var FileInfoData */
	public $data;


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
