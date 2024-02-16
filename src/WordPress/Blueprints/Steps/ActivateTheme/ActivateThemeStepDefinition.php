<?php

namespace WordPress\Blueprints\Steps\ActivateTheme;

use WordPress\Blueprints\Parser\Annotation\StepDefinition;

/**
 * @StepDefinition(id="activateTheme")
 */
class ActivateThemeStepDefinition {

	public function __construct(
		/**
		 * The name of the theme folder inside wp-content/themes/
		 */
		public string $themeFolderName
	) {
	}
}
