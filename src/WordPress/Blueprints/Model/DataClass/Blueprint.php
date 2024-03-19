<?php

namespace WordPress\Blueprints\Model\DataClass;

class Blueprint {
	/**
	 * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Version of WordPress to use. Also accepts URL to a WordPress zip file.
	 *
	 * @var string
	 */
	public $WordPressVersion;

	/**
	 * Slot for runtime–specific options, schema must be provided by the runtime.
	 *
	 * @var \ArrayObject
	 */
	public $runtime;

	/** @var BlueprintOnBoot */
	public $onBoot;

	/**
	 * PHP Constants to define on every request
	 *
	 * @var \ArrayObject
	 */
	public $constants = array();

	/**
	 * WordPress plugins to install and activate
	 *
	 * @var string[]|ResourceDefinitionInterface[]
	 */
	public $plugins = array();

	/**
	 * WordPress site options to define
	 *
	 * @var \ArrayObject
	 */
	public $siteOptions = array();

	/**
	 * The steps to run after every other operation in this Blueprint was executed.
	 *
	 * @var StepDefinitionInterface[]
	 */
	public $steps = array();


	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;

		return $this;
	}


	/**
	 * @param string $WordPressVersion
	 */
	public function setWordPressVersion( $WordPressVersion ) {
		$this->WordPressVersion = $WordPressVersion;

		return $this;
	}


	public function setRuntime( $runtime ) {
		$this->runtime = $runtime;

		return $this;
	}


	/**
	 * @param \WordPress\Blueprints\Model\DataClass\BlueprintOnBoot $onBoot
	 */
	public function setOnBoot( $onBoot ) {
		$this->onBoot = $onBoot;

		return $this;
	}


	public function setConstants( $constants ) {
		$this->constants = $constants;

		return $this;
	}


	/**
	 * @param mixed[] $plugins
	 */
	public function setPlugins( $plugins ) {
		$this->plugins = $plugins;

		return $this;
	}


	public function setSiteOptions( $siteOptions ) {
		$this->siteOptions = $siteOptions;

		return $this;
	}


	/**
	 * @param mixed[] $steps
	 */
	public function setSteps( $steps ) {
		$this->steps = $steps;

		return $this;
	}
}
