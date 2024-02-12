<?php

namespace WordPress\Blueprints\Steps\ActivateTheme;

use WordPress\Blueprints\Steps\BaseStepInput;

class ActivateThemeStepInput extends BaseStepInput {

	public function __construct(
		/**
		 * The name of the theme folder inside wp-content/themes/
		 */
		public string $themeFolderName
	) {
	}
}
