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
use WordPress\Blueprints\Model\DataClass\EnableMultisiteStep;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;
use WordPress\Blueprints\Model\DataClass\ImportFileStep;
use WordPress\Blueprints\Model\DataClass\InlineResource;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use WordPress\Blueprints\Model\DataClass\MvStep;
use WordPress\Blueprints\Model\DataClass\RmDirStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Model\DataClass\UnzipWordPressStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Resource\Resolver\FilesystemResourceResolver;
use WordPress\Blueprints\Resource\Resolver\InlineResourceResolver;
use WordPress\Blueprints\Resource\Resolver\ResourceResolverCollection;
use WordPress\Blueprints\Resource\Resolver\UrlResourceResolver;
use WordPress\Blueprints\Resource\ResourceManager;
use WordPress\Blueprints\Runner\Blueprint\BlueprintRunner;
use WordPress\Blueprints\Runner\Step\ActivatePluginStepRunner;
use WordPress\Blueprints\Runner\Step\ActivateThemeStepRunner;
use WordPress\Blueprints\Runner\Step\CpStepRunner;
use WordPress\Blueprints\Runner\Step\DefineSiteUrlStepRunner;
use WordPress\Blueprints\Runner\Step\DefineWpConfigConstsStepRunner;
use WordPress\Blueprints\Runner\Step\EnableMultisiteStepRunner;
use WordPress\Blueprints\Runner\Step\ImportFileStepRunner;
use WordPress\Blueprints\Runner\Step\InstallPluginStepRunner;
use WordPress\Blueprints\Runner\Step\InstallThemeStepRunner;
use WordPress\Blueprints\Runner\Step\MvStepRunner;
use WordPress\Blueprints\Runner\Step\RmDirStepRunner;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use WordPress\Blueprints\Runner\Step\RunPHPStepRunner;
use WordPress\Blueprints\Runner\Step\RunSQLStepRunner;
use WordPress\Blueprints\Runner\Step\RunWordPressInstallerStepRunner;
use WordPress\Blueprints\Runner\Step\SetSiteOptionsStepRunner;
use WordPress\Blueprints\Runner\Step\UnzipStepRunner;
use WordPress\Blueprints\Runner\Step\UnzipWordPressStepRunner;
use WordPress\Blueprints\Runner\Step\WPCLIStepRunner;
use WordPress\Blueprints\Runner\Step\WriteFileStepRunner;
use WordPress\Blueprints\Runtime\NativePHPRuntime;
use WordPress\Blueprints\Runtime\RuntimeInterface;
use WordPress\DataSource\FileSource;
use WordPress\DataSource\ProgressEvent;
use WordPress\DataSource\UrlSource;

class ContainerBuilder {

	const RUNTIME_NATIVE = 'native';
	const RUNTIME_PLAYGROUND = 'playground';
	const RUNTIME_WP_NOW = 'wp-now';
	const RUNTIMES = [
		self::RUNTIME_NATIVE,
		self::RUNTIME_PLAYGROUND,
		self::RUNTIME_WP_NOW,
	];

	protected $container;

	public function __construct() {
		$this->container = new Container();
	}


	public function build( RuntimeInterface $runtime ) {
		$container = $this->container;
		$container['runtime'] = function () use ( $runtime ) {
			return $runtime;
		};

		if ( $runtime instanceof NativePHPRuntime ) {
			$container['downloads_cache'] = function ( $c ) {
				return new FileCache();
			};
			$container['http_client'] = function ( $c ) {
				return HttpClient::create();
			};
			$container['progress_reporter'] = function ( $c ) {
				return function ( ProgressEvent $event ) {
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

			);
		};

		$container['blueprint.json_schema'] = function () {
			return json_decode( file_get_contents( __DIR__ . '/schema.json' ) );
		};

		$container['blueprint.parser'] = function ( $c ) {
			return new BlueprintParser(
				$c['resource.resolver'],
				$c['blueprint.json_schema']
			);
		};

		$container[ "step.runner." . UnzipStep::SLUG ] = function () {
			return new UnzipStepRunner();
		};
		$container[ "step.runner." . UnzipWordPressStep::SLUG ] = function () {
			return new UnzipWordPressStepRunner();
		};
		$container[ "step.runner." . WriteFileStep::SLUG ] = function () {
			return new WriteFileStepRunner();
		};
		$container[ "step.runner." . RunPHPStep::SLUG ] = function () {
			return new RunPHPStepRunner();
		};
		$container[ "step.runner." . DefineWpConfigConstsStep::SLUG ] = function () {
			return new DefineWpConfigConstsStepRunner();
		};
		$container[ "step.runner." . EnableMultisiteStep::SLUG ] = function () {
			return new EnableMultisiteStepRunner();
		};
		$container[ "step.runner." . DefineSiteUrlStep::SLUG ] = function () {
			return new DefineSiteUrlStepRunner();
		};
		$container[ "step.runner." . RmDirStep::SLUG ] = function () {
			return new RmDirStepRunner();
		};
		$container[ "step.runner." . RmStep::SLUG ] = function () {
			return new RmStepRunner();
		};
		$container[ "step.runner." . MvStep::SLUG ] = function () {
			return new MvStepRunner();
		};
		$container[ "step.runner." . CpStep::SLUG ] = function () {
			return new CpStepRunner();
		};
		$container[ "step.runner." . WPCLIStep::SLUG ] = function () {
			return new WPCLIStepRunner();
		};
		$container[ "step.runner." . SetSiteOptionsStep::SLUG ] = function () {
			return new SetSiteOptionsStepRunner();
		};
		$container[ "step.runner." . ActivatePluginStep::SLUG ] = function () {
			return new ActivatePluginStepRunner();
		};
		$container[ "step.runner." . ActivateThemeStep::SLUG ] = function () {
			return new ActivateThemeStepRunner();
		};
		$container[ "step.runner." . InstallPluginStep::SLUG ] = function () {
			return new InstallPluginStepRunner();
		};
		$container[ "step.runner." . InstallThemeStep::SLUG ] = function () {
			return new InstallThemeStepRunner();
		};
		$container[ "step.runner." . ImportFileStep::SLUG ] = function () {
			return new ImportFileStepRunner();
		};
		$container[ "step.runner." . RunWordPressInstallerStep::SLUG ] = function () {
			return new RunWordPressInstallerStepRunner();
		};
		$container[ "step.runner." . RunSQLStep::SLUG ] = function () {
			return new RunSQLStepRunner();
		};
		$container[ "resource.resolver." . UrlResource::SLUG ] = function ( $c ) {
			return new UrlResourceResolver( $c['data_source.url'] );
		};
		$container[ "resource.resolver." . FilesystemResource::SLUG ] = function () {
			return new FilesystemResourceResolver();
		};
		$container[ "resource.resolver." . InlineResource::SLUG ] = function () {
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

		// Add a progress listener to all data sources
		foreach ( $container->keys() as $key ) {
			if ( str_starts_with( $key, 'data_source.' ) ) {
				$container->extend( $key, function ( $urlSource, $c ) {
					$urlSource->events->addListener(
						ProgressEvent::class,
						$c['progress_reporter']
					);

					return $urlSource;
				} );
			}
		}

		return $container;
	}

}
