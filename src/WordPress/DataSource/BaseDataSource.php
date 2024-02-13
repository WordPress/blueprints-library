<?php

namespace WordPress\DataSource;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class BaseDataSource {

	public EventDispatcher $events;

	public function __construct() {
		$this->events = new EventDispatcher();
	}

	abstract public function stream( $resourceIdentifier );

}
