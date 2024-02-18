<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder;
use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;

class DefineSiteUrlStepRunner extends BaseStepRunner {

	function run( DefineSiteUrlStep $input ) {
		// @TODO: Don't manually construct the step object like this.
		//        There may be more required fields in the future.
		//        Instead, either remove this step, move the const-setting
		//        logic to another class with crisply defined dependencies,
		//        or provide a method similar to:
		//        $executionContext->createStepRunner( DefineWpConfigConstsStepRunner::class )
		$defineConstsHandler = new DefineWpConfigConstsStepRunner();
		$defineConstsHandler->setRuntime( $this->getRuntime() );
		$defineConstsHandler->run( ( new DefineWpConfigConstsStepBuilder() )
			->setConsts( [ 'WP_HOME' => $input->siteUrl, 'WP_SITEURL' => $input->siteUrl ] )
			->toDataObject() );
	}
}
