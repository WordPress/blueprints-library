<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;

class DefineSiteUrlStepRunner extends BaseStepRunner {

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep $input
	 */
	function run( $input ) {
		// @TODO: Don't manually construct the step object like this.
		// There may be more required fields in the future.
		// Instead, either remove this step, move the const-setting
		// logic to another class with crisply defined dependencies,
		// or provide a method similar to:
		// $executionContext->createStepRunner( DefineWpConfigConstsStepRunner::class )
		$defineConstsHandler = new DefineWpConfigConstsStepRunner();
		$defineConstsHandler->setRuntime( $this->getRuntime() );
		$defineConstsHandler->run(
			( new DefineWpConfigConstsStep() )
			->setConsts(
				array(
					'WP_HOME'    => $input->siteUrl,
					'WP_SITEURL' => $input->siteUrl,
				)
			)
		);
	}

	public function getDefaultCaption( $input ) {
		return 'Defining site URL';
	}
}
