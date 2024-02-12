<?php

namespace blueprints\src\WordPress\Blueprints\Steps\ActivateTheme;

use blueprints\src\WordPress\Blueprints\Steps\BaseStepInput;

class ActivateThemeStepInput extends BaseStepInput {

	public function __construct(
		/**
		 * The name of the theme folder inside wp-content/themes/
		 */
		public string $themeFolderName
	) {
	}
}
