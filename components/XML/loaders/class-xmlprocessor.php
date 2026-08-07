<?php

namespace WordPress\XML;

if ( file_exists( __DIR__ . '/../class-xmlprocessor.php' ) ) {
	require_once __DIR__ . '/../class-xmlprocessor.php';
} else {
	require_once __DIR__ . '/../PHP/class-phpxmlprocessor.php';
}

class XMLProcessor extends PHPXMLProcessor {}
