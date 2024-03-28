<?php

namespace unit\zip;

use PHPUnitTestCase;
use Symfony\Component\Filesystem\Path;
use function WordPress\Zip\zip_extract_to;

class ZipFunctionsTest extends PHPUnitTestCase {
	public function testIsImmuneToZipSlipVulnerability() {
		// zipped file named: "../../../../../../../../tmp/zip-slip-test.txt"
		$zip = __DIR__ . '/resources/zip-slip-test.zip';

		zip_extract_to( fopen( $zip, 'rb' ), dirname( $zip ) );

		$slipped_file = Path::canonicalize(__DIR__ . "../../../../../../../../tmp/zip-slip-test.txt");
		self::assertFileDoesNotExist( $slipped_file );
	}
}
