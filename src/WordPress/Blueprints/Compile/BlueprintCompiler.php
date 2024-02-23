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
use WordPress\Blueprints\Resource\Resolver\ResourceResolverInterface;

class BlueprintCompiler {

	public function __construct(
		protected $stepRunnerFactory,
		protected ResourceResolverInterface $resourceResolver
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
