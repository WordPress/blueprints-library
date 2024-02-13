<?php

namespace WordPress\DataSource;

class FileSource extends BaseDataSource {

	public function stream( $resourceIdentifier ) {
		$path = $resourceIdentifier;

		return fopen( $path, 'r' );
	}
}
