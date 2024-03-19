<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;

class DefineWpConfigConstsStepRunner extends BaseStepRunner {

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep $input
	 */
	function run( $input ) {
		$functions = file_get_contents( __DIR__ . '/DefineWpConfigConsts/functions.php' );

		return $this->getRuntime()->evalPhpInSubProcess(
			"$functions ?>" . '<?php
    $wp_config_path = "./wp-config.php";
	$consts = json_decode(getenv("CONSTS"), true);
	$wp_config = file_get_contents($wp_config_path);
	$new_wp_config = rewrite_wp_config_to_define_constants($wp_config, $consts);
	file_put_contents($wp_config_path, $new_wp_config);
',
			array(
				'CONSTS' => json_encode( $input->consts ),
			)
		);
	}

	public function getDefaultCaption( $input ) {
		return 'Defining wp-config constants';
	}
}
