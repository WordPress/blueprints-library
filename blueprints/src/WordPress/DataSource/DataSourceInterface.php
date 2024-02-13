<?php

namespace WordPress\DataSource;

interface DataSourceInterface {
	public function stream( $resourceIdentifier );
}
