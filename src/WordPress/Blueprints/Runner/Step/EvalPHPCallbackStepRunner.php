<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\EvalPHPCallbackStep;
use WordPress\Blueprints\Progress\Tracker;

class EvalPHPCallbackStepRunner extends BaseStepRunner {
	/**
	 * @param EvalPHPCallbackStep $input
	 * @param Tracker $tracker
	 */
	function run( EvalPHPCallbackStep $input, Tracker $tracker ) {
		if ( ! is_callable( $input->callback ) ) {
			throw new \InvalidArgumentException( "The 'callback' input property is not callable" );
		}
		$callback = $input->callback;

		return $callback( $this->getRuntime() );
	}
}
