<?php

namespace WordPress\Blueprints\Model\DataClass;

class BlueprintOnBoot
{
	/**
	 * The URL to navigate to after the blueprint has been run.
	 * @var string
	 */
	public $openUrl;

	/** @var bool */
	public $login;


	public function setOpenUrl(string $openUrl)
	{
		$this->openUrl = $openUrl;
		return $this;
	}


	public function setLogin(bool $login)
	{
		$this->login = $login;
		return $this;
	}
}
