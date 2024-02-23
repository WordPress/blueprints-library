<?php

namespace WordPress\Blueprints\Model\DataClass;

class Blueprint
{
	/**
	 * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
	 * @var string
	 */
	public $description;

	/**
	 * Version of WordPress to use. Also accepts URL to a WordPress zip file.
	 * @var string
	 */
	public $WordPressVersion;

	/**
	 * Slot for runtime–specific options, schema must be provided by the runtime.
	 * @var \ArrayObject
	 */
	public $runtime;

	/** @var BlueprintOnBoot */
	public $onBoot;

	/**
	 * PHP Constants to define on every request
	 * @var \ArrayObject
	 */
	public $constants;

	/**
	 * WordPress plugins to install and activate
	 * @var string[]|ResourceDefinitionInterface[]
	 */
	public $plugins = [];

	/**
	 * WordPress site options to define
	 * @var \ArrayObject
	 */
	public $siteOptions;

	/**
	 * The steps to run after every other operation in this Blueprint was executed.
	 * @var StepDefinitionInterface[]
	 */
	public $steps = [];


	public function setDescription(string $description)
	{
		$this->description = $description;
		return $this;
	}


	public function setWordPressVersion(string $WordPressVersion)
	{
		$this->WordPressVersion = $WordPressVersion;
		return $this;
	}


	public function setRuntime(iterable $runtime)
	{
		$this->runtime = $runtime;
		return $this;
	}


	public function setOnBoot(BlueprintOnBoot $onBoot)
	{
		$this->onBoot = $onBoot;
		return $this;
	}


	public function setConstants(iterable $constants)
	{
		$this->constants = $constants;
		return $this;
	}


	public function setPlugins(array $plugins)
	{
		$this->plugins = $plugins;
		return $this;
	}


	public function setSiteOptions(iterable $siteOptions)
	{
		$this->siteOptions = $siteOptions;
		return $this;
	}


	public function setSteps(array $steps)
	{
		$this->steps = $steps;
		return $this;
	}
}
