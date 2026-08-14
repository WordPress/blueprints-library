<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\URL\WPURL;

class WPURLTest extends TestCase {

	/**
	 * @dataProvider provider_test_parse_with_base_url
	 */
	public function test_parse_with_base_url( $url, $base_url, $expected_result ) {
		$parsed_url = WPURL::parse( $url, $base_url );
		$this->assertNotFalse( $parsed_url, 'URL parsing failed' );
		$this->assertEquals( $expected_result, $parsed_url->toString() );
	}

	public static function provider_test_parse_with_base_url() {
		return array(
			'Relative file path with base URL' => array(
				'nodejs-development-environment.md',
				'https://wordpress.org',
				'https://wordpress.org/nodejs-development-environment.md',
			),
		);
	}

	/**
	 * @dataProvider provider_test_replace_base_url_trailing_slash
	 */
	public function test_replace_base_url_trailing_slash( $url, $options, $expected_result ) {
		$result = WPURL::replace_base_url( $url, $options );
		$this->assertNotFalse( $result, 'replace_base_url() returned false' );
		$this->assertEquals( $expected_result, (string) $result );
	}

	public static function provider_test_replace_base_url_trailing_slash() {
		return array(
			'Origin-only URL without trailing slash preserves no-slash style' => array(
				'https://example.com/',
				array(
					'old_base_url' => 'https://example.com',
					'new_base_url' => 'https://newsite.com',
					'raw_url'      => 'https://example.com',
					'is_relative'  => false,
				),
				'https://newsite.com',
			),
			'Origin-only URL with trailing slash preserves slash'             => array(
				'https://example.com/',
				array(
					'old_base_url' => 'https://example.com',
					'new_base_url' => 'https://newsite.com',
					'raw_url'      => 'https://example.com/',
					'is_relative'  => false,
				),
				'https://newsite.com/',
			),
			'Origin-only URL without slash, new base has a path'             => array(
				'https://example.com/',
				array(
					'old_base_url' => 'https://example.com',
					'new_base_url' => 'https://newsite.com/blog/',
					'raw_url'      => 'https://example.com',
					'is_relative'  => false,
				),
				'https://newsite.com/blog',
			),
			'URL with path and no trailing slash – existing behavior'        => array(
				'https://example.com/page',
				array(
					'old_base_url' => 'https://example.com',
					'new_base_url' => 'https://newsite.com',
					'raw_url'      => 'https://example.com/page',
					'is_relative'  => false,
				),
				'https://newsite.com/page',
			),
			'URL with path and trailing slash – existing behavior'           => array(
				'https://example.com/page/',
				array(
					'old_base_url' => 'https://example.com',
					'new_base_url' => 'https://newsite.com',
					'raw_url'      => 'https://example.com/page/',
					'is_relative'  => false,
				),
				'https://newsite.com/page/',
			),
		);
	}

	/**
	 * @dataProvider provider_replace_base_url_source_preserving_raw_url
	 */
	public function test_replace_base_url_returns_source_preserving_raw_url(
		$raw_url,
		$old_base_url,
		$new_base_url,
		$expected_raw_url,
		$include_raw_url = true
	) {
		$url     = WPURL::parse( $raw_url, $old_base_url );
		$options = array(
			'old_base_url' => $old_base_url,
			'new_base_url' => $new_base_url,
			'is_relative'  => ! WPURL::can_parse( $raw_url ),
		);
		if ( $include_raw_url ) {
			$options['raw_url'] = $raw_url;
		}
		$result = WPURL::replace_base_url(
			$url,
			$options
		);

		$this->assertNotFalse( $result );
		$this->assertSame( $expected_raw_url, $result->new_source_preserving_raw_url );
		if ( null === $expected_raw_url ) {
			return;
		}

		$this->assertSame(
			WPURL::parse( $expected_raw_url, $new_base_url )->toString(),
			$result->new_url->toString()
		);
	}

	public static function provider_replace_base_url_source_preserving_raw_url() {
		return array(
			'absolute'          => array(
				'http://old.example/media/file?x=1#section',
				'http://old.example/media',
				'https://new.example/assets',
				'https://new.example/assets/file?x=1#section',
			),
			'protocol-relative' => array(
				'//old.example/media/file',
				'http://old.example/media',
				'https://new.example/assets',
				'//new.example/assets/file',
			),
			'root-relative'     => array(
				'/media/file',
				'http://old.example/media',
				'https://new.example/assets',
				'/assets/file',
			),
			'percent-encoded source segment' => array(
				'/m%65dia/file',
				'http://old.example/media',
				'https://new.example/assets',
				'/assets/file',
			),
			'path-relative'     => array(
				'file',
				'http://old.example/',
				'https://new.example/assets/',
				'/assets/file',
			),
			'query-only'        => array(
				'?x=1',
				'http://old.example/media',
				'https://new.example/assets',
				'/assets/?x=1',
			),
			'empty relative URL' => array(
				'',
				'http://old.example/media/',
				'https://new.example/assets/',
				'/assets/',
			),
			'exact absolute base' => array(
				'http://old.example/media',
				'http://old.example/media',
				'https://new.example/assets',
				'https://new.example/assets',
			),
			'explicit default port' => array(
				'http://old.example:80/media/file',
				'http://old.example/media',
				'https://old.example/assets',
				'https://old.example/assets/file',
			),
			'non-default source port' => array(
				'http://old.example:81/media/file',
				'http://old.example/media',
				'https://new.example/assets',
				'https://new.example/assets/file',
			),
			'no-op keeps same input' => array(
				'http://OLD.example/media/%7euser',
				'http://old.example/media',
				'http://old.example/media',
				'http://OLD.example/media/%7euser',
			),
			'file URL' => array(
				'file://old.example/media/file',
				'http://old.example/media',
				'https://new.example/assets',
				null,
			),
			'explicit scheme without slashes' => array(
				'http:media/file',
				'http://old.example/media',
				'https://new.example/assets',
				null,
			),
			'parent-directory-relative path' => array(
				'../file',
				'http://old.example/media',
				'https://new.example/assets',
				null,
			),
			'backslashes in mapped components' => array(
				'http://old.example\\media/file',
				'http://old.example/media',
				'https://new.example/assets',
				null,
			),
			'encoded slash at the mapped boundary' => array(
				'/media%2Fa',
				'http://old.example/media',
				'https://new.example/',
				null,
			),
			'parent segment hides a different lexical base' => array(
				'http://old.example/x/../media/file',
				'http://old.example/media',
				'http://old.example/foo/media',
				null,
			),
			'encoded slash and dot hide different lexical segments' => array(
				'/a%2Fb/./file',
				'http://old.example/a/b',
				'https://new.example/assets',
				null,
			),
			'raw URL not supplied' => array(
				'http://old.example/media/file',
				'http://old.example/media',
				'https://new.example/assets',
				null,
				false,
			),
		);
	}

	public function test_replace_base_url_source_preserving_raw_url_follows_the_semantic_result() {
		$raw_url = 'http://user@old.example/media/file';
		$result  = WPURL::replace_base_url(
			WPURL::parse( $raw_url ),
			array(
				'old_base_url' => 'http://old.example/media',
				'new_base_url' => 'https://new.example/assets',
				'raw_url'      => $raw_url,
				'is_relative'  => false,
			)
		);

		$this->assertNotFalse( $result );
		$this->assertSame( (string) $result, $result->new_source_preserving_raw_url );
	}
}
