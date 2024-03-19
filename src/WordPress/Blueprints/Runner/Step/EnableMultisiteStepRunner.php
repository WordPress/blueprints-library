<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\EnableMultisiteStep;

class EnableMultisiteStepRunner extends BaseStepRunner {
	/**
	 * @param EnableMultisiteStep $input
	 */
	function run( $input ) {
		throw new \LogicException( 'Not implemented yet' );

		// @TODO:
		// return $this->getRuntime()->runShellCommand(
		// [
		// 'php',
		// 'wp-cli.phar',
		// 'core',
		// 'multisite-convert',
		// @TODO: Base path should come from the runtime, e.g.
		// Playground need to provide the /scope:0.892173/ value
		// '--base=/wordpress',
		// ]
		// );
	}
}
