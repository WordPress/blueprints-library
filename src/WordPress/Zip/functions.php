<?php

namespace WordPress\Zip;

function zip_read_entry( $fp ) {
	return ZipStreamReader::readEntry( $fp );
}

function zip_extract_to( $fp, $toPath ) {
	while ( $entry = zip_read_entry( $fp ) ) {
		if ( ! $entry->isFileEntry() ) {
			continue;
		}
		$path   = $toPath . '/' . sanitize_path( $entry->path );
		$parent = dirname( $path );
		if ( ! is_dir( $parent ) ) {
			mkdir( $parent, 0777, true );
		}

		if ( $entry->isDirectory ) {
			if ( ! is_dir( $path ) ) {
				mkdir( $path, 0777, true );
			}
		} else {
			file_put_contents( $path, $entry->bytes );
		}
	}
}

// @TODO: Find a more reliable technique
function sanitize_path( $path ) {
	if ( empty( $path ) ) {
		return '';
	}

	return preg_replace( '#/\.+(/|$)#', '/', $path );
}
