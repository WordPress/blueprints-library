<?php

namespace WordPress\Blueprints\StepHandler\ActivateTheme;

use WordPress\Blueprints\StepHandler\BaseStepInput;

class ActivateThemeStepInput extends BaseStepInput {

	public function __construct(
		/**
		 * The name of the theme folder inside wp-content/themes/
		 */
		public string $themeFolderName
	) {
	}
}
