<?php

namespace WordPress\Blueprints\Dependency;

use Doctrine\Common\Annotations\AnnotationReader;
use Pimple\Container;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Validation;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\Parsing\Blueprint;
use WordPress\Blueprints\Parsing\Form\DynamicFormHelper;
use WordPress\Blueprints\Parsing\Form\DynamicType;
use WordPress\Blueprints\Parsing\Parser;
use WordPress\Blueprints\Resources\PathResource;
use WordPress\Blueprints\Resources\URLResource;
use WordPress\Blueprints\Steps\Mkdir\MkdirStep;
use WordPress\Blueprints\Steps\Unzip\UnzipStep;
use WordPress\Blueprints\Steps\WriteFile\WriteFileStep;
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

	private Container $container;

	public function __construct() {
		$this->container = new Container();
	}

	public function build( $runtime ) {
		if ( ! in_array( $runtime, self::RUNTIMES ) ) {
			throw new \InvalidArgumentException( 'Invalid runtime' );
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

		$container['data_source.url']  = function ( $c ) {
			return new UrlSource( $c['http_client'], $c['downloads_cache'] );
		};
		$container['data_source.file'] = function ( $c ) {
			return new FileSource();
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

		$container['annotation_reader'] = function ( $c ) {
			return new AnnotationReader();
		};

		$container['validator']    = function ( $c ) {
			return Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
		};
		$container['form_builder'] = function ( $c ) {
			return Forms::createFormFactoryBuilder()
			            ->addExtension( new ValidatorExtension( $c['validator'] ) );
		};

		$container['json_schema_compiler'] = function ( $c ) {
			return new Parser( $c['available_steps'], $c['validator'] );
		};

		$container['available_steps'] = $container->factory( function ( $c ) {
			$steps = [];
			foreach ( $c->keys() as $key ) {
				if ( str_starts_with( $key, 'step_meta.' ) ) {
					$steps[ $c[ $key ]->slug ] = $c[ $key ];
				}
			}

			return $steps;
		} );

		$container['blueprint.form_factory'] = function ( $c ) {
			return function () use ( $c ) {
				return $c['form_builder']->getFormFactory()->create( DynamicType::class, new Blueprint(), [
					'data_class'          => Blueprint::class,
					'dynamic_form_helper' => $c['forms.dynamic_form_helper'],
				] );
			};
		};

		$container['forms.dynamic_form_helper'] = function ( $c ) {
			return new DynamicFormHelper(
				$c['available_steps'],
				$c['available_resources']
			);
		};

		$container['blueprint.parser'] = function ( $c ) {
			return new Parser( $c['blueprint.form_factory'] );
		};

		self::registerBlueprintStep( UnzipStep::class );
		self::registerBlueprintStep( WriteFileStep::class );
		self::registerBlueprintStep( MkdirStep::class );


		$container['available_resources'] = $container->factory( function ( $c ) {
			$steps = [];
			foreach ( $c->keys() as $key ) {
				if ( str_starts_with( $key, 'resource_meta.' ) ) {
					$steps[ $c[ $key ]->slug ] = $c[ $key ];
				}
			}

			return $steps;
		} );

		self::registerResourceType( URLResource::class );
		self::registerResourceType( PathResource::class );

		return $container;
	}

	public function registerResourceType( string $dataClass ) {
		if ( ! class_exists( $dataClass ) ) {
			throw new \InvalidArgumentException( "Resource data class $dataClass does not exist" );
		}

		if ( defined( "$dataClass::SLUG" ) ) {
			$slug = $dataClass::SLUG;
		} else {
			$slug = ( new \ReflectionClass( $dataClass ) )->getShortName();
			$slug = lowercase_until_uppercase( $slug );
			if ( str_ends_with( strtolower( $slug ), 'resource' ) ) {
				$slug = substr( $slug, 0, - 8 );
			}
		}
		$this->container["resource_meta.$slug"] = new ResourceMeta(
			$slug,
			$dataClass
		);
	}

	public function registerBlueprintStep( string $stepClass, string $inputClass = null, callable $factory = null ) {
		$container = $this->container;

		if ( defined( "$stepClass::SLUG" ) ) {
			$slug = $stepClass::SLUG;
		} else {
			$slug = ( new \ReflectionClass( $stepClass ) )->getShortName();
			$slug = lowercase_until_uppercase( $slug );
			if ( str_ends_with( strtolower( $slug ), 'step' ) ) {
				$slug = substr( $slug, 0, - 4 );
			}
		}

		if ( ! $factory ) {
			$factory = function () use ( $stepClass ) {
				return new $stepClass();
			};
		}
		if ( ! $inputClass ) {
			// Get the type of the first parameter of the execute method
			$reflection = new \ReflectionMethod( $stepClass, 'execute' );
			$parameters = $reflection->getParameters();
			if ( $parameters[0] ) {
				$inputClass = $parameters[0]->getType()->getName();
			}
		}
		if ( ! class_exists( $inputClass ) ) {
			throw new \InvalidArgumentException( "Could not determine input class for $stepClass" );
		}
		$container["step.$slug"]      = $container->factory( $factory );
		$container["step_meta.$slug"] = new StepMeta(
			$slug,
			$stepClass,
			$inputClass
		);
	}

}


function lowercase_until_uppercase( string $string ) {
	$len = strlen( $string );
	for ( $i = 0; $i < $len; $i ++ ) {
		if ( ctype_lower( $string[ $i ] ) ) {
			return strtolower( substr( $string, 0, $i ) ) . substr( $string, $i );
		}
	}

	return $string;
}
