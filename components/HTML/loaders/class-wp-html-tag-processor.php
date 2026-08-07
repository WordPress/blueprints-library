<?php
/**
 * Public HTML tag processor loader.
 *
 * @package WordPress
 * @subpackage HTML-API
 */

if ( file_exists( __DIR__ . '/../class-wp-html-tag-processor.php' ) ) {
	require_once __DIR__ . '/../class-wp-html-tag-processor.php';
} else {
	require_once __DIR__ . '/../PHP/class-wp-html-php-tag-processor.php';
}

class WP_HTML_Tag_Processor extends WP_HTML_PHP_Tag_Processor {}
