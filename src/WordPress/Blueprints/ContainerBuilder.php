<?php

namespace WordPress\Blueprints;

use InvalidArgumentException;
use Pimple\Container;
use ReflectionProperty;
use Symfony\Component\HttpClient\HttpClient;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\Model\DataClass\ActivatePluginStep;
use WordPress\Blueprints\Model\DataClass\ActivateThemeStep;
use WordPress\Blueprints\Model\DataClass\CpStep;
use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\EnableMultisiteStep;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;
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
use WordPress\Blueprints\Model\DataClass\StepInterface;
use WordPress\Blueprints\Model\DataClass\UnzipStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\ResourceResolver\FilesystemResourceResolver;
use WordPress\Blueprints\ResourceResolver\InlineResourceResolver;
use WordPress\Blueprints\ResourceResolver\ResourceResolverCollection;
use WordPress\Blueprints\ResourceResolver\ResourceResolverInterface;
use WordPress\Blueprints\ResourceResolver\UrlResourceResolver;
use WordPress\Blueprints\StepRunner\BaseStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\ActivatePluginStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\ActivateThemeStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\CpStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\DefineSiteUrlStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\DefineWpConfigConstsStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\EnableMultisiteStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\ImportFileStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\InstallPluginStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\InstallThemeStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\MvStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\RmDirStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\RmStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\RunPHPStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\RunSQLStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\RunWordPressInstallerStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\SetSiteOptionsStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\UnzipStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\WPCLIStepRunner;
use WordPress\Blueprints\StepRunner\Implementation\WriteFileStepRunner;
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


	public function build( $runtime ) {
		if ( ! in_array( $runtime, self::RUNTIMES ) ) {
			throw new InvalidArgumentException( 'Invalid runtime' );
		}
		$container = $this->container;

		switch ( $runtime ) {
			case self::RUNTIME_NATIVE:
				$container['downloads_cache']   = function ( $c ) {
					return new FileCache();
				};
				$container['http_client']       = function ( $c ) {
					return HttpClient::create();
				};
				$container['progress_reporter'] = function ( $c ) {
					return function ( ProgressEvent $event ) {
						echo $event->url . ' ' . $event->downloadedBytes . '/' . $event->totalBytes . "                         \r";
					};
				};
				break;
			case self::RUNTIME_PLAYGROUND:
				$container['downloads_cache']   = function ( $c ) {
					// @TODO
				};
				$container['http_client']       = function ( $c ) {
					// @TODO
				};
				$container['progress_reporter'] = function ( $c ) {
					// @TODO
					// post_message_to_js();
				};
				break;
			case self::RUNTIME_WP_NOW:
				$container['downloads_cache']   = function ( $c ) {
					return new FileCache( '/cache' );
				};
				$container['http_client']       = function ( $c ) {
					// @TODO
				};
				$container['progress_reporter'] = function ( $c ) {
					// @TODO
					// post_message_to_js ? Or use the same progress bar as the
					// the native runtime?
				};
				break;
		}

		$container[ "step.runner." . UnzipStep::SLUG ]                 = function () {
			return new UnzipStepRunner();
		};
		$container[ "step.runner." . WriteFileStep::SLUG ]             = function () {
			return new WriteFileStepRunner();
		};
		$container[ "step.runner." . RunPHPStep::SLUG ]                = function () {
			return new RunPHPStepRunner();
		};
		$container[ "step.runner." . DefineWpConfigConstsStep::SLUG ]  = function () {
			return new DefineWpConfigConstsStepRunner();
		};
		$container[ "step.runner." . EnableMultisiteStep::SLUG ]       = function () {
			return new EnableMultisiteStepRunner();
		};
		$container[ "step.runner." . DefineSiteUrlStep::SLUG ]         = function () {
			return new DefineSiteUrlStepRunner();
		};
		$container[ "step.runner." . RmDirStep::SLUG ]                 = function () {
			return new RmDirStepRunner();
		};
		$container[ "step.runner." . RmStep::SLUG ]                    = function () {
			return new RmStepRunner();
		};
		$container[ "step.runner." . MvStep::SLUG ]                    = function () {
			return new MvStepRunner();
		};
		$container[ "step.runner." . CpStep::SLUG ]                    = function () {
			return new CpStepRunner();
		};
		$container[ "step.runner." . WPCLIStep::SLUG ]                 = function () {
			return new WPCLIStepRunner();
		};
		$container[ "step.runner." . SetSiteOptionsStep::SLUG ]        = function () {
			return new SetSiteOptionsStepRunner();
		};
		$container[ "step.runner." . ActivatePluginStep::SLUG ]        = function () {
			return new ActivatePluginStepRunner();
		};
		$container[ "step.runner." . ActivateThemeStep::SLUG ]         = function () {
			return new ActivateThemeStepRunner();
		};
		$container[ "step.runner." . InstallPluginStep::SLUG ]         = function () {
			return new InstallPluginStepRunner();
		};
		$container[ "step.runner." . InstallThemeStep::SLUG ]          = function () {
			return new InstallThemeStepRunner();
		};
		$container[ "step.runner." . ImportFileStep::SLUG ]            = function () {
			return new ImportFileStepRunner();
		};
		$container[ "step.runner." . RunWordPressInstallerStep::SLUG ] = function () {
			return new RunWordPressInstallerStepRunner();
		};
		$container[ "step.runner." . RunSQLStep::SLUG ]                = function () {
			return new RunSQLStepRunner();
		};

		$container[ "resource.resolver." . UrlResource::SLUG ]        = function ( $c ) {
			return new UrlResourceResolver( $c['data_source.url'] );
		};
		$container[ "resource.resolver." . FilesystemResource::SLUG ] = function () {
			return new FilesystemResourceResolver();
		};
		$container[ "resource.resolver." . InlineResource::SLUG ]     = function () {
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
