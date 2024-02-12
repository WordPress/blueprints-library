<?php

namespace WordPress\Blueprints\Steps;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class BaseStep {
    public EventDispatcher $events;
    abstract public static function getInputClass() : string;

    public function __construct() {
        $this->events = new EventDispatcher();
    }

    abstract public function execute();
}
