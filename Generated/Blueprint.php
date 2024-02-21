<?php

namespace WordPress\Blueprints\Generated;

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
	 * The preferred PHP and WordPress versions to use.
	 * @var BlueprintPreferredVersions
	 */
	public $preferredVersions;

	/** @var BlueprintFeatures */
	public $features;

	/**
	 * PHP Constants to define on every request
	 * @var array<string, string>
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
	 * The PHP extensions to use.
	 * @var string[]
	 */
	public $phpExtensionBundles;

	/**
	 * The steps to run after every other operation in this Blueprint was executed.
	 * @var StepDefinitionInterface[]
	 */
	public $steps;
}
