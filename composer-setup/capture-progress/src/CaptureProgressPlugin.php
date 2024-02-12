<?php

namespace WordPress\Composer\CaptureProgress;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent;

class CaptureProgressPlugin implements PluginInterface, EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return array(
			'pre-file-download'              => 'onPreFileDownload',
			PluginEvents::PRE_FILE_DOWNLOAD  => array(
				array( 'onPreFileDownload', 0 ),
			),
			PluginEvents::POST_FILE_DOWNLOAD => array(
				array( 'onPostFileDownload', 0 ),
			),
		);
	}

	public function onPreFileDownload( PreFileDownloadEvent $event ) {
		echo "Downloading " . $event->getProcessedUrl() . "...\n";
	}

	public function onPostFileDownload( PreFileDownloadEvent $event ) {
		echo "Downloading " . $event->getProcessedUrl() . "...\n";
	}

	public function activate( Composer $composer, IOInterface $io ) {
		$composer->getEventDispatcher()->addListener(
			PluginEvents::PRE_FILE_DOWNLOAD,
			array( $this, 'onPreFileDownload' )
		);
	}

	public function deactivate( Composer $composer, IOInterface $io ) {
		// TODO: Implement deactivate() method.
	}

	public function uninstall( Composer $composer, IOInterface $io ) {
		// TODO: Implement uninstall() method.
	}
}
