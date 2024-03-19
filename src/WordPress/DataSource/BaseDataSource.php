<?php

namespace WordPress\DataSource;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class BaseDataSource implements DataSourceInterface {

	public $events;

	public function __construct() {
		$this->events = new EventDispatcher();
	}

	abstract public function stream( $resourceIdentifier );
}
