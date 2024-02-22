<?php

namespace WordPress\Blueprints\Model\DataClass;

class Blueprint
{
	/**
	 * The URL to navigate to after the blueprint has been run.
	 * @var string
	 */
	public $landingPage;

	/**
	 * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
	 * @var string
	 */
	public $description;

	/**
	 * Slot for runtime–specific options, schema must be provided by the runtime.
	 * @var \ArrayObject
	 */
	public $runtime;

	/** @var BlueprintOnBoot */
	public $onBoot;

	/**
	 * The preferred WordPress version to use. If not specified, the latest supported version will be used
	 * @var string
	 */
	public $wpVersion;

	/**
	 * PHP Constants to define on every request
	 * @var \ArrayObject
	 */
	public $constants;

	/**
	 * WordPress plugins to install and activate
	 * @var string[]|ResourceDefinitionInterface[]
	 */
	public $plugins;

	/**
	 * WordPress site options to define
	 * @var BlueprintSiteOptions
	 */
	public $siteOptions;

	/**
	 * The steps to run after every other operation in this Blueprint was executed.
	 * @var StepDefinitionInterface[]
	 */
	public $steps;


	public function setLandingPage(string $landingPage)
	{
		$this->landingPage = $landingPage;
		return $this;
	}


	public function setDescription(string $description)
	{
		$this->description = $description;
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


	public function setWpVersion(string $wpVersion)
	{
		$this->wpVersion = $wpVersion;
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


	public function setSiteOptions(BlueprintSiteOptions $siteOptions)
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
