<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallThemeStepOptions
{
	/**
	 * Whether to activate the theme after installing it.
	 * @var bool
	 */
	public $activate = true;


	public function setActivate(bool $activate)
	{
		$this->activate = $activate;
		return $this;
	}
}
