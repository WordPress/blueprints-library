<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WordPressInstallationOptions;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Resources\Resolver\ResourceResolverInterface;
use WordPress\Blueprints\Progress\Tracker;

class BlueprintCompiler {
	protected $stepRunnerFactory;
	protected $resourceResolver;

	public function __construct(
		$stepRunnerFactory,
		ResourceResolverInterface $resourceResolver
	) {
		$this->resourceResolver  = $resourceResolver;
		$this->stepRunnerFactory = $stepRunnerFactory;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 */
	public function compile( $blueprint ): CompiledBlueprint {
		$blueprint->steps = array_merge( $this->expandShorthandsIntoSteps( $blueprint ), $blueprint->steps );

		$progressTracker = new Tracker();
		$stepsStage      = $progressTracker->stage( 0.6 );
		$resourcesStage  = $progressTracker->stage( 0.4 );

		return new CompiledBlueprint(
			$this->compileSteps( $blueprint, $stepsStage ),
			$this->compileResources( $blueprint, $resourcesStage ),
			$progressTracker,
			$stepsStage
		);
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 */
	protected function expandShorthandsIntoSteps( $blueprint ) {
		// @TODO: This duplicates the logic in BlueprintComposer.
		// It cannot be easily reused because of the dichotomy between the
		// Step and Step model classes. Let's alter the code generation
		// to only generate a single model class for each schema object.
		$additional_steps = array();
		if ( $blueprint->WordPressVersion ) {
			$additional_steps[] = ( new DownloadWordPressStep() )
				->setWordPressZip(
					( new UrlResource() )
						->setUrl( $blueprint->WordPressVersion )
				);
			$additional_steps[] = ( new InstallSqliteIntegrationStep() )
				->setSqlitePluginZip(
					( new UrlResource() )
						->setUrl( 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' )
				);
			// @TODO: stream_select times out here:
			$additional_steps[] = ( new WriteFileStep() )
				->setPath( 'wp-cli.phar' )
				->setData( ( new UrlResource() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) );
			$additional_steps[] = ( new RunWordPressInstallerStep() )
				->setOptions( new WordPressInstallationOptions() );
		}
		if ( $blueprint->constants && $blueprint->constants->count() > 0 ) {
			$step               = new DefineWpConfigConstsStep();
			$step->consts       = $blueprint->constants;
			$additional_steps[] = $step;
		}
		if ( $blueprint->plugins ) {
			foreach ( $blueprint->plugins as $plugin ) {
				$step                = new InstallPluginStep();
				$step->pluginZipFile = $plugin;
				$additional_steps[]  = $step;
			}
		}
		if ( $blueprint->siteOptions && $blueprint->siteOptions->count() > 0) {
			$step = new SetSiteOptionsStep();
			$step->setOptions( $blueprint->siteOptions );
			$additional_steps[] = $step;
		}

		return $additional_steps;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 * @param \WordPress\Blueprints\Progress\Tracker          $progress
	 */
	protected function compileSteps( $blueprint, $progress ) {
		$stepRunnerFactory = $this->stepRunnerFactory;
		// Compile, ensure all the runners may be created and configured
		$totalProgressWeight = 0;
		foreach ( $blueprint->steps as $step ) {
			$totalProgressWeight += $step->progress->weight ?? 1;
		}

		$compiledSteps = array();
		foreach ( $blueprint->steps as $step ) {
			$runner              = $stepRunnerFactory( $step->step );
			$stepProgressTracker = $progress->stage(
				( $step->progress->weight ?? 1 ) / $totalProgressWeight,
				$step->progress->caption ?? $runner->getDefaultCaption( $step )
			);

			/** @var $runner \WordPress\Blueprints\Runner\Step\BaseStepRunner */
			$compiledSteps[] = new CompiledStep(
				$step,
				$runner,
				$stepProgressTracker
			);
		}

		return $compiledSteps;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 * @param \WordPress\Blueprints\Progress\Tracker          $progress
	 */
	protected function compileResources( $blueprint, $progress ) {
		$resources = array();
		$this->findResources( $blueprint, $resources );

		$totalProgressWeight = count( $resources );
		$compiledResources   = array();
		foreach ( $resources as $path => list( $declaration, $resource ) ) {
			/** @var $resource ResourceDefinitionInterface */
			$compiledResources[ $path ] = new CompiledResource(
				$declaration,
				$resource,
				$progress->stage(
					1 / $totalProgressWeight,
					$resource->caption ?? 'Fetching a resource'
				)
			);
		}

		return $compiledResources;
	}

	// Find all the resources in the blueprint
	protected function findResources( $blueprintFragment, &$resources, $path = '', $parseUrls = false ) {
		if ( $parseUrls && is_string( $blueprintFragment ) ) {
			$resources[ $path ] = array(
				$blueprintFragment,
				$this->resourceResolver->parseUrl( $blueprintFragment ),
			);
		} elseif ( $blueprintFragment instanceof ResourceDefinitionInterface ) {
			$resources[ $path ] = array(
				$blueprintFragment,
				$blueprintFragment,
			);
		} elseif ( is_object( $blueprintFragment ) ) {
			// Check if the @var annotation mentions a ResourceDefinitionInterface
			foreach ( get_object_vars( $blueprintFragment ) as $key => $value ) {
				$reflection = new \ReflectionProperty( $blueprintFragment, $key );
				$reflection->setAccessible( true );
				$docComment      = $reflection->getDocComment();
				$parseNestedUrls = false;
				if ( preg_match( '/@var\s+([^\s]+)/', $docComment, $matches ) ) {
					$className = $matches[1];
					if (
						false !== strpos( $className, 'string' ) &&
						false !== strpos( $className, 'ResourceDefinitionInterface' )
					) {
						$parseNestedUrls = true;
					}
				}
				$this->findResources( $value, $resources, $path . '->' . $key, $parseNestedUrls );
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
