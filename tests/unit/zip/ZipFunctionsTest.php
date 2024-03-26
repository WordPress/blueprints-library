<?php

namespace unit\zip;

use PHPUnitTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use ZipArchive;
use function WordPress\Zip\zip_extract_to;

class ZipFunctionsTest extends PHPUnitTestCase {
	public function testIsImmuneToZipSlipVulnerability() {
		$filesystem = new Filesystem();

		$filename = __DIR__ . 'tmp/zip-slip-test.zip';
		$filesystem->mkdir( dirname( $filename ) );

		$zip = new ZipArchive();
		$zip->open( $filename, ZipArchive::CREATE );
		$zip->addFromString( "../../../../../../../../tmp/zip-slip-test.txt" . time(), "zip slip test" );
		$zip->close();

		zip_extract_to( fopen( $filename, 'rb' ), dirname( $filename ) );

		$slipped_dir = Path::canonicalize(__DIR__ . "../../../../../../../../tmp");
		self::assertDirectoryDoesNotExist( $slipped_dir );

		$filesystem->remove( dirname( $filename ) );
	}
}