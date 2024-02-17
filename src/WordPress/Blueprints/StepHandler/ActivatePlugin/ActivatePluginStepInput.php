<?php

namespace WordPress\Blueprints\StepHandler\ActivatePlugin;

use WordPress\Blueprints\StepHandler\BaseStepInput;

class ActivatePluginStepInput extends BaseStepInput {

	public function __construct(
		/**
		 * The name of the plugin folder inside wp-content/plugins/
		 */
		public string $pluginPath
	) {
	}

}
