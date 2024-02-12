<?php

namespace WordPress\Blueprints;

class ProgressCaptionEvent extends \Symfony\Contracts\EventDispatcher\Event {
    public function __construct(
        public string $caption
    ) {}
}
