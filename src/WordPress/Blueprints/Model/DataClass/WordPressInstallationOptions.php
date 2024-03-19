<?php

namespace WordPress\Blueprints\Model\DataClass;

class WordPressInstallationOptions {

	/** @var string */
	public $adminUsername;

	/** @var string */
	public $adminPassword = 'admin';


	/**
	 * @param string $adminUsername
	 */
	public function setAdminUsername( $adminUsername ) {
		$this->adminUsername = $adminUsername;
		return $this;
	}


	/**
	 * @param string $adminPassword
	 */
	public function setAdminPassword( $adminPassword ) {
		$this->adminPassword = $adminPassword;
		return $this;
	}
}
