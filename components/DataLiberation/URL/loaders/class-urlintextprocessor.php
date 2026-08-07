<?php

namespace WordPress\DataLiberation\URL;

if ( file_exists( __DIR__ . '/../class-urlintextprocessor.php' ) ) {
	require_once __DIR__ . '/../class-urlintextprocessor.php';
} else {
	require_once __DIR__ . '/../PHP/class-phpurlintextprocessor.php';
}

class URLInTextProcessor extends PHPURLInTextProcessor {}
