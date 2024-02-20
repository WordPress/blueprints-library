<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\Builder\DownloadWordPressStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallSqliteIntegrationStepBuilder;
use WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlResourceBuilder;
use WordPress\Blueprints\Model\Builder\WordPressInstallationOptionsBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;

class BlueprintCompiler {

	public function __construct(
		protected $stepRunnerFactory,
	) {
	}

	public function compile( Blueprint $blueprint ): CompiledBlueprint {
		$blueprint->steps = array_merge( $this->expandShorthandsIntoSteps( $blueprint ), $blueprint->steps );

		return new CompiledBlueprint(
			$this->compileSteps( $blueprint ),
			$this->compileResources( $blueprint )
		);
	}

	protected function expandShorthandsIntoSteps( Blueprint $blueprint ) {
		// @TODO: This duplicates the logic in BlueprintComposer.
		//        It cannot be easily reused because of the dichotomy between the
		//        StepBuilder and Step model classes. Let's alter the code generation
		//        to only generate a single model class for each schema object.
		$additional_steps = [];
		if ( $blueprint->wpVersion ) {
			$additional_steps[] = ( new DownloadWordPressStepBuilder() )
				->setWordPressZip(
					( new UrlResourceBuilder() )
						->setUrl( $blueprint->wpVersion )
				)
				->toDataObject();
			$additional_steps[] = ( new InstallSqliteIntegrationStepBuilder() )
				->setSqlitePluginZip(
					( new UrlResourceBuilder() )
						->setUrl( 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' )
				)->toDataObject();
			$additional_steps[] = ( new WriteFileStepBuilder() )
				->setPath( 'wp-cli.phar' )
				->setData( ( new UrlResourceBuilder() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) )
				->toDataObject();
			$additional_steps[] = ( new RunWordPressInstallerStepBuilder() )
				->setOptions( new WordPressInstallationOptionsBuilder() )
				->toDataObject();
		}
		if ( $blueprint->constants ) {
			$step = new DefineWpConfigConstsStep();
			$step->consts = $blueprint->constants;
			$additional_steps[] = $step;
		}
		if ( $blueprint->plugins ) {
			foreach ( $blueprint->plugins as $plugin ) {
				$step = new InstallPluginStep();
				$step->pluginZipFile = $plugin;
				$additional_steps[] = $step;
			}
		}
		if ( $blueprint->siteOptions ) {
			$step = new SetSiteOptionsStep();
			$step->options = (object) $blueprint->siteOptions;
			$additional_steps[] = $step;
		}

		return $additional_steps;
	}

	protected function compileSteps( Blueprint $blueprint ) {
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

	protected function compileResources( Blueprint $blueprint ) {
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
