<?php

namespace WordPress\Blueprints\Model\DataClass;

class BlueprintSiteOptions
{
	/**
	 * The site title
	 * @var string
	 */
	public $blogname;


	public function setBlogname(string $blogname)
	{
		$this->blogname = $blogname;
		return $this;
	}
}
