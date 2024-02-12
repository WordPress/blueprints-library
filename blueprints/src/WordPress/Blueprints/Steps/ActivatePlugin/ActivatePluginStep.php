<?php

namespace blueprints\src\WordPress\Blueprints\Steps\ActivatePlugin;

use blueprints\src\WordPress\Blueprints\ProgressCaptionEvent;
use blueprints\src\WordPress\Blueprints\Steps\BaseStep;
use function WordPress\Blueprints\Steps\ActivatePlugin\run_php_file;

require_once __DIR__ . '/Utils.php';

class Step extends BaseStep {
	public const INPUT_TYPE = ActivatePluginStepInput::class;

	public function __construct(
		private ActivatePluginStepInput $input,
	) {
		parent::__construct();
	}

	public function execute() {
		$this->events->dispatch( new ProgressCaptionEvent( "Activating {$this->input->pluginPath}" ) );

		return run_php_file( __DIR__ . '/wp_activate_plugin.php', [
			'PLUGIN_PATH' => $this->input->pluginPath,
			'DOCROOT'     => '/wordpress',
		] );
	}
}
