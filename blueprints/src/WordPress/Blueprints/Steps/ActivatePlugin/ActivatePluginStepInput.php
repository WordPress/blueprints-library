<?php

namespace blueprints\src\WordPress\Blueprints\Steps\ActivatePlugin;

use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class ActivatePluginStepInput extends BaseStepInput {

	public function __construct(
		/**
		 * The name of the plugin folder inside wp-content/plugins/
		 */
		public string $pluginPath
	) {
	}

}
