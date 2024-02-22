<?php

namespace WordPress\Blueprints;

use InvalidArgumentException;
use Pimple\Container;
use Symfony\Component\HttpClient\HttpClient;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\Compile\BlueprintCompiler;
use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use WordPress\Blueprints\Model\DataClass\ActivateThemeStep;
use WordPress\Blueprints\Model\DataClass\CpStep;
use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\EnableMultisiteStep;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Model\DataClass\ImportFileStep;
use WordPress\Blueprints\Model\DataClass\InlineResource;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use WordPress\Blueprints\Model\DataClass\MvStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\BlueprintMapper;
use WordPress\Blueprints\Resources\Resolver\FilesystemResourceResolver;
use WordPress\Blueprints\Resources\Resolver\InlineResourceResolver;
use WordPress\Blueprints\Resources\Resolver\ResourceResolverCollection;
use WordPress\Blueprints\Resources\Resolver\UrlResourceResolver;
use WordPress\Blueprints\Resources\ResourceManager;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;
use WordPress\Blueprints\Runner\Step\ActivatePluginStepRunner;
use WordPress\Blueprints\Runner\Step\ActivateThemeStepRunner;
use WordPress\Blueprints\Runner\Step\CpStepRunner;
use WordPress\Blueprints\Runner\Step\DefineSiteUrlStepRunner;
use WordPress\Blueprints\Runner\Step\DefineWpConfigConstsStepRunner;
use WordPress\Blueprints\Runner\Step\DownloadWordPressStepRunner;
use WordPress\Blueprints\Runner\Step\EnableMultisiteStepRunner;
use WordPress\Blueprints\Runner\Step\ImportFileStepRunner;
use WordPress\Blueprints\Runner\Step\InstallPluginStepRunner;
use WordPress\Blueprints\Runner\Step\InstallSqliteIntegrationStepRunner;
use WordPress\Blueprints\Runner\Step\InstallThemeStepRunner;
use WordPress\Blueprints\Runner\Step\MvStepRunner;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runner\Step\RunPHPStepRunner;
use WordPress\Blueprints\Runner\Step\RunSQLStepRunner;
use WordPress\Blueprints\Runner\Step\RunWordPressInstallerStepRunner;
use WordPress\Blueprints\Runner\Step\SetSiteOptionsStepRunner;
use WordPress\Blueprints\Runner\Step\UnzipStepRunner;
use WordPress\Blueprints\Runner\Step\WPCLIStepRunner;
use WordPress\Blueprints\Runner\Step\WriteFileStepRunner;
use WordPress\Blueprints\Runtime\Runtime;
use WordPress\Blueprints\Runtime\RuntimeInterface;
use WordPress\DataSource\FileSource;
use WordPress\DataSource\PlaygroundFetchSource;
use WordPress\DataSource\DataSourceProgressEvent;
use WordPress\DataSource\UrlSource;

class ContainerBuilder {

	const ENVIRONMENT_NATIVE = 'native';
	const ENVIRONMENT_PLAYGROUND = 'playground';
	const ENVIRONMENT_WP_NOW = 'wp-now';
	const ENVIRONMENTS = [
		self::ENVIRONMENT_NATIVE,
		self::ENVIRONMENT_PLAYGROUND,
		self::ENVIRONMENT_WP_NOW,
	];

	protected $container;

	public function __construct() {
		$this->container = new Container();
	}


	public function build( string $environment, RuntimeInterface $runtime ) {
		$container = $this->container;
		$container['runtime'] = function () use ( $runtime ) {
			return $runtime;
		};

		if ( $environment === static::ENVIRONMENT_NATIVE ) {
			$container['downloads_cache'] = function ( $c ) {
				return new FileCache();
			};
			$container['http_client'] = function ( $c ) {
				return HttpClient::create();
			};
			$container['progress_reporter'] = function ( $c ) {
				return function ( DataSourceProgressEvent $event ) {
					echo $event->url . ' ' . $event->downloadedBytes . '/' . $event->totalBytes . "                         \r";
				};
			};
			$container[ "resource.resolver." . UrlResource::DISCRIMINATOR ] = function ( $c ) {
				return new UrlResourceResolver( $c['data_source.url'] );
			};
		} elseif ( $environment === static::ENVIRONMENT_PLAYGROUND ) {
			$container[ "resource.resolver." . UrlResource::DISCRIMINATOR ] = function ( $c ) {
				return new UrlResourceResolver( $c['data_source.playground_fetch'] );
			};
			$container['progress_reporter'] = function ( $c ) {
				return function ( DataSourceProgressEvent $event ) {
					echo $event->url . ' ' . $event->downloadedBytes . '/' . $event->totalBytes . "                         \r";
				};
			};
		} else {
			throw new InvalidArgumentException( "Not implemented yet" );
		}

		$container['blueprint.engine'] = function ( $c ) {
			return new Engine(
				$c['blueprint.parser'],
				$c['blueprint.compiler'],
				$c['blueprint.runner'],
			);
		};

		$container['blueprint.runner'] = function ( $c ) {
			return new BlueprintRunner(
				$c['runtime'],
				function () use ( $c ) {
					return $c['blueprint.resource_manager'];
				}
			);
		};

		$container['blueprint.resource_manager'] = function ( $c ) {
			return new ResourceManager(
				$c['resource.resolver']
			);
		};

		$container['blueprint.compiler'] = function ( $c ) {
			return new BlueprintCompiler(
				$c['step.runner_factory'],
				$c['resource.resolver']
			);
		};

		$container['blueprint.json_schema_path'] = function () {
			return __DIR__ . '/schema.json';
		};
		$container['blueprint.json_schema'] = function ( $c ) {
			return json_decode( file_get_contents( $c['blueprint.json_schema_path'] ) );
		};

		$container['blueprint.validator'] = function ( $c ) {
			return new BlueprintValidator(
				$c['blueprint.json_schema_path']
			);
		};
		$container['blueprint.mapper'] = function ( $c ) {
			return new BlueprintMapper();
		};
		$container['blueprint.parser'] = function ( $c ) {
			return new BlueprintParser(
				$c['blueprint.validator'],
				$c['blueprint.mapper']
			);
		};

		$container[ "step.runner." . InstallSqliteIntegrationStep::DISCRIMINATOR ] = function () {
			return new InstallSqliteIntegrationStepRunner();
		};
		$container[ "step.runner." . DownloadWordPressStep::DISCRIMINATOR ] = function () {
			return new DownloadWordPressStepRunner();
		};
		$container[ "step.runner." . UnzipStep::DISCRIMINATOR ] = function () {
			return new UnzipStepRunner();
		};
		$container[ "step.runner." . WriteFileStep::DISCRIMINATOR ] = function () {
			return new WriteFileStepRunner();
		};
		$container[ "step.runner." . RunPHPStep::DISCRIMINATOR ] = function () {
			return new RunPHPStepRunner();
		};
		$container[ "step.runner." . DefineWpConfigConstsStep::DISCRIMINATOR ] = function () {
			return new DefineWpConfigConstsStepRunner();
		};
		$container[ "step.runner." . EnableMultisiteStep::DISCRIMINATOR ] = function () {
			return new EnableMultisiteStepRunner();
		};
		$container[ "step.runner." . DefineSiteUrlStep::DISCRIMINATOR ] = function () {
			return new DefineSiteUrlStepRunner();
		};
		$container[ "step.runner." . RmStep::DISCRIMINATOR ] = function () {
			return new RmStepRunner();
		};
		$container[ "step.runner." . MvStep::DISCRIMINATOR ] = function () {
			return new MvStepRunner();
		};
		$container[ "step.runner." . CpStep::DISCRIMINATOR ] = function () {
			return new CpStepRunner();
		};
		$container[ "step.runner." . WPCLIStep::DISCRIMINATOR ] = function () {
			return new WPCLIStepRunner();
		};
		$container[ "step.runner." . SetSiteOptionsStep::DISCRIMINATOR ] = function () {
			return new SetSiteOptionsStepRunner();
		};
		$container[ "step.runner." . ActivatePluginStep::DISCRIMINATOR ] = function () {
			return new ActivatePluginStepRunner();
		};
		$container[ "step.runner." . ActivateThemeStep::DISCRIMINATOR ] = function () {
			return new ActivateThemeStepRunner();
		};
		$container[ "step.runner." . InstallPluginStep::DISCRIMINATOR ] = function () {
			return new InstallPluginStepRunner();
		};
		$container[ "step.runner." . InstallThemeStep::DISCRIMINATOR ] = function () {
			return new InstallThemeStepRunner();
		};
		$container[ "step.runner." . ImportFileStep::DISCRIMINATOR ] = function () {
			return new ImportFileStepRunner();
		};
		$container[ "step.runner." . RunWordPressInstallerStep::DISCRIMINATOR ] = function () {
			return new RunWordPressInstallerStepRunner();
		};
		$container[ "step.runner." . RunSQLStep::DISCRIMINATOR ] = function () {
			return new RunSQLStepRunner();
		};

		$container[ "resource.resolver." . FilesystemResource::DISCRIMINATOR ] = function () {
			return new FilesystemResourceResolver();
		};
		$container[ "resource.resolver." . InlineResource::DISCRIMINATOR ] = function () {
			return new InlineResourceResolver();
		};

		$container['resource.supported_resolvers'] = function ( $c ) {
			$ResourceResolvers = [];
			foreach ( $c->keys() as $key ) {
				if ( str_starts_with( $key, 'resource.resolver.' ) ) {
					$ResourceResolvers[] = $c[ $key ];
				}
			}

			return $ResourceResolvers;
		};

		$container['resource.resolver'] = function ( $c ) {
			return new ResourceResolverCollection( $c['resource.supported_resolvers'] );
		};

		$container['resource.manager'] = $container->factory( function ( $c ) {
			return new ResourceManager(
				$c['resource.resolver']
			);
		} );

		$container['step.runner_factory'] = function ( $c ) {
			return function ( $slug ) use ( $c ) {
				if ( ! isset( $c["step.runner.$slug"] ) ) {
					throw new InvalidArgumentException( "No runner registered for step {$slug}" );
				}

				return $c["step.runner.$slug"];
			};
		};

		$container['data_source.url'] = function ( $c ) {
			return new UrlSource( $c['http_client'], $c['downloads_cache'] );
		};
		$container['data_source.playground_fetch'] = function ( $c ) {
			return new PlaygroundFetchSource();
		};

		// Add a progress listener to all data sources
//		foreach ( $container->keys() as $key ) {
//			if ( str_starts_with( $key, 'data_source.' ) ) {
//				$container->extend( $key, function ( $urlSource, $c ) {
//					$urlSource->events->addListener(
//						ProgressEvent::class,
//						$c['progress_reporter']
//					);
//
//					return $urlSource;
//				} );
//			}
//		}

		return $container;
	}

}
