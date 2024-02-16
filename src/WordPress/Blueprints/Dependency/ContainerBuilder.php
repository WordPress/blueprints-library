<?php

namespace WordPress\Blueprints\Dependency;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Pimple\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\Common\Annotations\Reader;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\Parser\Annotation\ResourceDefinition;
use WordPress\Blueprints\Parser\Annotation\StepDefinition;
use WordPress\Blueprints\Parser\Blueprint;
use WordPress\Blueprints\Parser\Form\Discriminator\DiscriminatedDataClassRegistry;
use WordPress\Blueprints\Parser\Form\DataClassToForm;
use WordPress\Blueprints\Parser\Parser;
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
			// Then create the annotation reader and cached reader:
			$annotationReader = new AnnotationReader();
			$cache            = new ArrayAdapter();

			return new PsrCachedReader( $annotationReader, $cache );
		};

		$container['validator']            = function ( $c ) {
			return Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
		};
		$container['form.factory_builder'] = function ( $c ) {
			return Forms::createFormFactoryBuilder()
			            ->addExtension( new ValidatorExtension( $c['validator'] ) );
		};
		$container['form.factory']         = function ( $c ) {
			return $c['form.factory_builder']->getFormFactory();
		};

		$container['json_schema_compiler'] = function ( $c ) {
			return new Parser( $c['available_steps'], $c['validator'] );
		};

		$container['blueprint.parser'] = function ( $c ) {
			return new Parser( $c['blueprint.form_factory'] );
		};

		$container['blueprint.form_factory'] = function ( $c ) {
			return function () use ( $c ) {
				return $c['blueprint.parser.data_class_to_form']->buildFormForDataClass(
					$c['form.factory']->createBuilder( FormType::class, null, [
						'data_class' => Blueprint::class,
					] )
				);
			};
		};

		$container['blueprint.parser.data_class_to_form'] = function ( $c ) {
			return new DataClassToForm(
				$c['blueprint.parser.discriminated_class_registry'],
				$c['annotation_reader'],
			);
		};

		$container['blueprint.parser.discriminated_class_registry'] = function ( $c ) {
			return new DiscriminatedDataClassRegistry(
				$c['annotation_reader']
			);
		};

		self::registerBlueprintStep( UnzipStep::class );
		self::registerBlueprintStep( WriteFileStep::class );
		self::registerBlueprintStep( MkdirStep::class );

		self::registerResourceType( URLResource::class );
		self::registerResourceType( PathResource::class );

		$container['available_steps'] = $container->factory( function ( $c ) {
			$steps = [];
			foreach ( $c->keys() as $key ) {
				if ( str_starts_with( $key, 'step_meta.' ) ) {
					$steps[ $c[ $key ]->slug ] = $c[ $key ];
				}
			}

			return $steps;
		} );

		$container['available_resources'] = $container->factory( function ( $c ) {
			$steps = [];
			foreach ( $c->keys() as $key ) {
				if ( str_starts_with( $key, 'resource_meta.' ) ) {
					$steps[ $c[ $key ]->slug ] = $c[ $key ];
				}
			}

			return $steps;
		} );

		return $container;
	}

	public function registerResourceType( string $dataClass ) {
		if ( ! class_exists( $dataClass ) ) {
			throw new \InvalidArgumentException( "Resource data class $dataClass does not exist" );
		}

		$this->container['blueprint.parser.discriminated_class_registry']->register( $dataClass );
		$slug = $this->container['annotation_reader']->getClassAnnotation( new \ReflectionClass( $dataClass ),
			ResourceDefinition::class )->id;

		$this->container["resource_meta.$slug"] = new ResourceMeta(
			$slug,
			$dataClass
		);
	}

	public function registerBlueprintStep( string $stepClass, string $inputClass = null, callable $factory = null ) {
		$container = $this->container;

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

		$container['blueprint.parser.discriminated_class_registry']->register( $inputClass );

		$slug = $container['annotation_reader']->getClassAnnotation( new \ReflectionClass( $inputClass ),
			StepDefinition::class )->id;

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
