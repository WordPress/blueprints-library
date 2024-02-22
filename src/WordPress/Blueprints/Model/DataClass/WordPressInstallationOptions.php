<?php

namespace WordPress\Blueprints\Model\DataClass;

class WordPressInstallationOptions
{
	/** @var string */
	public $adminUsername;

	/** @var string */
	public $adminPassword;


	public function setAdminUsername(string $adminUsername)
	{
		$this->adminUsername = $adminUsername;
		return $this;
	}


	public function setAdminPassword(string $adminPassword)
	{
		$this->adminPassword = $adminPassword;
		return $this;
	}
}
