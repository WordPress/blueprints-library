<?php

namespace WordPress\Blueprints\Compile;


use WordPress\Blueprints\Model\Dirty\DownloadWordPressStep;
use WordPress\Blueprints\Model\InternalValidated\FileReferenceInterface;
use WordPress\Blueprints\Model\InternalValidated\ValidBlueprint;
use WordPress\Blueprints\Model\InternalValidated\ValidDefineWpConfigConstsStep;
use WordPress\Blueprints\Model\InternalValidated\ValidDownloadWordPressStep;
use WordPress\Blueprints\Model\InternalValidated\ValidInstallPluginStep;
use WordPress\Blueprints\Model\InternalValidated\ValidInstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\InternalValidated\ValidRunWordPressInstallerStep;
use WordPress\Blueprints\Model\InternalValidated\ValidSetSiteOptionsStep;
use WordPress\Blueprints\Model\InternalValidated\ValidUrlResource;
use WordPress\Blueprints\Model\InternalValidated\ValidWordPressInstallationOptions;
use WordPress\Blueprints\Model\InternalValidated\ValidWriteFileStep;

class BlueprintCompiler {

	public function __construct(
		protected $stepRunnerFactory,
	) {
	}

	public function compile( ValidBlueprint $blueprint ): CompiledBlueprint {
		$blueprint->steps = array_merge( $this->expandShorthandsIntoSteps( $blueprint ), $blueprint->steps );

		return new CompiledBlueprint(
			$this->compileSteps( $blueprint ),
			$this->compileResources( $blueprint )
		);
	}

	protected function expandShorthandsIntoSteps( ValidBlueprint $blueprint ) {
		// @TODO: This duplicates the logic in BlueprintComposer.
		//        It cannot be easily reused because of the dichotomy between the
		//        StepBuilder and Step model classes. Let's alter the code generation
		//        to only generate a single model class for each schema object.
		$additional_steps = [];
		if ( $blueprint->wpVersion ) {
			$additional_steps[] = new ValidDownloadWordPressStep(
				new ValidUrlResource( $blueprint->wpVersion )
			);
			$additional_steps[] = new ValidInstallSqliteIntegrationStep(
				new ValidUrlResource( 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' )
			);
			$additional_steps[] = new ValidWriteFileStep(
				'wp-config.php',
				new ValidUrlResource( 'https://playground.wordpress.net/wp-cli.phar' )
			);
			// @TODO use Valid* types as arguments
//			$additional_steps[] = new ValidRunWordPressInstallerStep(
//				new ValidWordPressInstallationOptions()
//			);
		}
		if ( $blueprint->constants ) {
			$additional_steps[] = new ValidDefineWpConfigConstsStep( $blueprint->constants );
		}
		if ( $blueprint->plugins ) {
			foreach ( $blueprint->plugins as $plugin ) {
				$additional_steps[] = new ValidInstallPluginStep( $plugin );
			}
		}
		if ( $blueprint->siteOptions ) {
			$additional_steps[] = new ValidSetSiteOptionsStep(
				(object) $blueprint->siteOptions
			);
		}

		return $additional_steps;
	}

	protected function compileSteps( ValidBlueprint $blueprint ) {
		$stepRunnerFactory = $this->stepRunnerFactory;
		// Compile, ensure all the runners may be created and configured
		$compiledSteps = [];
		foreach ( $blueprint->steps as $step ) {
			$runner = $stepRunnerFactory( $step->step );
			/** @var $runner \WordPress\Blueprints\Runner\Step\BaseStepRunner */
			$compiledSteps[] = new CompiledStep(
				$step,
				$runner
			);
		}

		return $compiledSteps;
	}

	protected function compileResources( ValidBlueprint $blueprint ) {
		$resources = [];
		$this->findResources( $blueprint, $resources );

		return $resources;
	}

	// Find all the resources in the blueprint
	protected function findResources( $blueprintFragment, &$resources, $path = '' ) {
		if ( $blueprintFragment instanceof FileReferenceInterface ) {
			$resources[ $path ] = $blueprintFragment;
		} elseif ( is_object( $blueprintFragment ) ) {
			foreach ( get_object_vars( $blueprintFragment ) as $key => $value ) {
				$this->findResources( $value, $resources, $path . '->' . $key );
			}
		} elseif ( is_array( $blueprintFragment ) ) {
			foreach ( $blueprintFragment as $k => $v ) {
				$this->findResources( $v, $resources, $path . '[' . $k . ']' );
			}
		} else {
			return $blueprintFragment;
		}
	}

}
