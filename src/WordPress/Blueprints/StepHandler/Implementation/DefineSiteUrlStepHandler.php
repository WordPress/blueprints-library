<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder;
use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;

class DefineSiteUrlStepHandler extends BaseStepHandler {

	function execute( DefineSiteUrlStep $input ) {
		// @TODO: Don't manually construct the step object like this.
		//        There may be more required fields in the future.
		//        Instead, either remove this step, move the const-setting
		//        logic to another class with crisply defined dependencies,
		//        or provide a method similar to:
		//        $executionContext->createStepHandler( DefineWpConfigConstsStepHandler::class )
		$defineConstsHandler = new DefineWpConfigConstsStepHandler();
		$defineConstsHandler->setRuntime( $this->getRuntime() );
		$defineConstsHandler->execute( ( new DefineWpConfigConstsStepBuilder() )
			->setConsts( [ 'WP_HOME' => $input->siteUrl, 'WP_SITEURL' => $input->siteUrl ] )
			->toDataObject() );
	}
}
