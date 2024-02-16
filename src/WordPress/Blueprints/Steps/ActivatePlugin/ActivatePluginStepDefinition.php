<?php

namespace WordPress\Blueprints\Steps\ActivatePlugin;

use WordPress\Blueprints\Parser\Annotation\StepDefinition;

/**
 * @StepDefinition(id="activatePlugin")
 */
class ActivatePluginStepDefinition {

	public function __construct(
		/**
		 * The name of the plugin folder inside wp-content/plugins/
		 */
		public string $pluginPath
	) {
	}

}
