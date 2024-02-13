<?php

namespace WordPress\Blueprints;

use Pimple\Container;
use Symfony\Component\HttpClient\HttpClient;
use WordPress\Blueprints\Cache\FileCache;
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

	static public function build( $runtime ) {
		if ( ! in_array( $runtime, self::RUNTIMES ) ) {
			throw new \InvalidArgumentException( 'Invalid runtime' );
		}

		$container = new Container();
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

		return $container;
	}

}
