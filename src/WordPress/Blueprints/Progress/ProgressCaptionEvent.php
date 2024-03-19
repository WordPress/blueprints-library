<?php

namespace WordPress\Blueprints\Progress;

class ProgressCaptionEvent extends \Symfony\Contracts\EventDispatcher\Event {
	/**
  * @var string
  */
 public $caption;
 public function __construct(string $caption)
 {
     $this->caption = $caption;
 }
}
