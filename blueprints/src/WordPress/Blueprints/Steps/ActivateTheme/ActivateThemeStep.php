<?php

namespace WordPress\Blueprints\Steps\ActivateTheme;

use WordPress\Blueprints\Steps\BaseStep;
use WordPress\Blueprints\Steps\ActivateTheme\ActivateThemeStepInput;
use WordPress\Blueprints\Steps\ActivateTheme\ProgressCaptionEvent;
use function WordPress\Blueprints\Steps\ActivateTheme\run_php_file;

require_once __DIR__ . '/Utils.php';

class ActivateThemeStep extends BaseStep {
	public const INPUT_TYPE = ActivateThemeStepInput::class;

	public function __construct(
		private ActivateThemeStepInput $input,
	) {
		parent::__construct();
	}

	public function execute() {
		$this->events->dispatch( new ProgressCaptionEvent( "Activating {$this->input->themeFolderName}" ) );

		return run_php_file( __DIR__ . '/wp_activate_theme.php', [
			'THEME_FOLDER_NAME' => $this->input->themeFolderName,
			'DOCROOT'           => '/wordpress',
		] );
	}
}
