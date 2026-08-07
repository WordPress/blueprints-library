<?php
/**
 * Public HTML processor loader.
 *
 * @package WordPress
 * @subpackage HTML-API
 */

if ( ! class_exists( 'WP_HTML_Tag_Processor', false ) ) {
	require_once __DIR__ . '/class-wp-html-tag-processor.php';
}

require_once __DIR__ . '/PHP/class-wp-html-php-processor.php';

class WP_HTML_Processor extends WP_HTML_PHP_Processor {}
