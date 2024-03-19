<?php

namespace WordPress\Blueprints\Model\DataClass;

class BlueprintOnBoot {

	/**
	 * The URL to navigate to after the blueprint has been run.
	 *
	 * @var string
	 */
	public $openUrl;

	/** @var bool */
	public $login;


	/**
	 * @param string $openUrl
	 */
	public function setOpenUrl( $openUrl ) {
		$this->openUrl = $openUrl;
		return $this;
	}


	/**
	 * @param bool $login
	 */
	public function setLogin( $login ) {
		$this->login = $login;
		return $this;
	}
}
