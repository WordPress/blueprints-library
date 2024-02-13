<?php

namespace WordPress\Blueprints\Steps\ActivatePlugin;

use WordPress\Blueprints\ProgressCaptionEvent;
use WordPress\Blueprints\Steps\BaseStep;
use function Playground\run_php_file;

require_once __DIR__ . '/Utils.php';

class ActivatePluginStep extends BaseStep {

	/** @var ActivatePluginStepInput */
	protected $args;

	public function execute() {
		$this->events->dispatch( new ProgressCaptionEvent( "Activating {$this->args->pluginPath}" ) );

		return run_php_file( __DIR__ . '/wp_activate_plugin.php', [
			'PLUGIN_PATH' => $this->args->pluginPath,
			'DOCROOT'     => '/wordpress',
		] );
	}

}
