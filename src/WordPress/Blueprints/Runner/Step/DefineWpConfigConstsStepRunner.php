<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;

class DefineWpConfigConstsStepRunner extends BaseStepRunner {

	function run( DefineWpConfigConstsStep $input ) {
		$functions = file_get_contents( __DIR__ . '/DefineWpConfigConsts/functions.php' );

		return $this->getRuntime()->evalPhpInSubProcess(
			"$functions ?>" . <<<'PHP'
<?php
    $wp_config_path = "./wp-config.php";
	$consts = json_decode(getenv("CONSTS"), true);
	$wp_config = file_get_contents($wp_config_path);
	$new_wp_config = rewrite_wp_config_to_define_constants($wp_config, $consts);
	file_put_contents($wp_config_path, $new_wp_config);
PHP,
			[
				'CONSTS' => json_encode( $input->consts ),
			]
		);
	}

	public function getDefaultCaption( $input ): null|string {
		return "Defining wp-config constants";
	}
}
