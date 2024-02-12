<?php

namespace WordPress\Blueprints\Steps\Mkdir;

use WordPress\Blueprints\Steps\BaseStepInput;

class MkdirStepInput extends BaseStepInput {
    public function __construct(
        public string $path
    ) {}
}
