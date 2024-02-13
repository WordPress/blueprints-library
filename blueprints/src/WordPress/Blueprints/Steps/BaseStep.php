<?php

namespace WordPress\Blueprints\Steps;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class BaseStep {
	public EventDispatcher $events;
	protected $args;

	public function __construct() {
		$this->events = new EventDispatcher();
	}

	public function setArgs( $args ) {
		$this->args = $args;
	}
	
	abstract public function execute();
}
