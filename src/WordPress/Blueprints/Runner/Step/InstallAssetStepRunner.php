<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Progress\Tracker;
use function WordPress\Blueprints\list_files;
use function WordPress\Blueprints\move_files_from_directory_to_directory;
use function WordPress\Zip\zip_extract_to;


class InstallAssetStepRunner extends BaseStepRunner {

	protected function unzipAssetTo( $zipResource, $targetPath ) {
		if ( ! file_exists( $targetPath ) ) {
			mkdir( $targetPath, 0777, true );
		}
		$this->getRuntime()->withTemporaryDirectory(
			function ( $tmpPath ) use ( $zipResource, $targetPath ) {
				zip_extract_to( $this->getResource( $zipResource ), $tmpPath );
				$extractedFiles               = list_files( $tmpPath, $omitDotFiles = true );
				$onlyExtractedSingleDirectory = count( $extractedFiles ) === 1 && is_dir( $tmpPath . '/' . $extractedFiles[0] );
				$moveFromPath                 = $onlyExtractedSingleDirectory ? "$tmpPath/$extractedFiles[0]" : $tmpPath;

				move_files_from_directory_to_directory(
					$moveFromPath,
					$this->getRuntime()->resolvePath( $targetPath )
				);
			}
		);
	}
}
