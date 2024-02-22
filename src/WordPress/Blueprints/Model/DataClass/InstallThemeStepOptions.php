<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallThemeStepOptions
{
	/**
	 * Whether to activate the theme after installing it.
	 * @var bool
	 */
	public $activate;


	public function setActivate(bool $activate)
	{
		$this->activate = $activate;
		return $this;
	}
}
