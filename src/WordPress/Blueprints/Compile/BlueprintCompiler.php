<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\Blueprint;
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
    protected ResourceResolverInterface $resourceResolver;

	public function __construct(
		$stepRunnerFactory,
		ResourceResolverInterface $resourceResolver
	) {
		$this->resourceResolver = $resourceResolver;
		$this->stepRunnerFactory = $stepRunnerFactory;
	}

	public function compile( Blueprint $blueprint ): CompiledBlueprint {
		$blueprint->steps = array_merge( $this->expandShorthandsIntoSteps( $blueprint ), $blueprint->steps );

		$progressTracker = new Tracker();
		$stepsStage = $progressTracker->stage( 0.6 );
		$resourcesStage = $progressTracker->stage( 0.4 );

		return new CompiledBlueprint(
			$this->compileSteps( $blueprint, $stepsStage ),
			$this->compileResources( $blueprint, $resourcesStage ),
			$progressTracker,
			$stepsStage
		);
	}

	protected function expandShorthandsIntoSteps( Blueprint $blueprint ) {
		// @TODO: This duplicates the logic in BlueprintComposer.
		//        It cannot be easily reused because of the dichotomy between the
		//        Step and Step model classes. Let's alter the code generation
		//        to only generate a single model class for each schema object.
		$additional_steps = [];
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
			$additional_steps[] = ( new WriteFileStep() )
				->setPath( 'wp-cli.phar' )
				->setData( ( new UrlResource() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) );
			$additional_steps[] = ( new RunWordPressInstallerStep() )
				->setOptions( new WordPressInstallationOptions() );
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
			$step->setOptions( $blueprint->siteOptions );
			$additional_steps[] = $step;
		}

		return $additional_steps;
	}

	protected function compileSteps( Blueprint $blueprint, Tracker $progress ) {
		$stepRunnerFactory = $this->stepRunnerFactory;
		// Compile, ensure all the runners may be created and configured
		$totalProgressWeight = 0;
		foreach ( $blueprint->steps as $step ) {
			$totalProgressWeight += $step->progress->weight ?? 1;
		}

		$compiledSteps = [];
		foreach ( $blueprint->steps as $step ) {
			$runner = $stepRunnerFactory( $step->step );
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

	protected function compileResources( Blueprint $blueprint, Tracker $progress ) {
		$resources = [];
		$this->findResources( $blueprint, $resources );

		$totalProgressWeight = count( $resources );
		$compiledResources = [];
		foreach ( $resources as $path => [$declaration, $resource] ) {
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
			$resources[ $path ] = [
				$blueprintFragment,
				$this->resourceResolver->parseUrl( $blueprintFragment ),
			];
		} elseif ( $blueprintFragment instanceof ResourceDefinitionInterface ) {
			$resources[ $path ] = [
				$blueprintFragment,
				$blueprintFragment,
			];
		} elseif ( is_object( $blueprintFragment ) ) {
			// Check if the @var annotation mentions a ResourceDefinitionInterface
			foreach ( get_object_vars( $blueprintFragment ) as $key => $value ) {
				$reflection = new \ReflectionProperty( $blueprintFragment, $key );
				$docComment = $reflection->getDocComment();
				$parseNestedUrls = false;
				if ( preg_match( '/@var\s+([^\s]+)/', $docComment, $matches ) ) {
					$className = $matches[1];
					if (
						str_contains( $className, 'string' ) &&
						str_contains( $className, 'ResourceDefinitionInterface' )
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
