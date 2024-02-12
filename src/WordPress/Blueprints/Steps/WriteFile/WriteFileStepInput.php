<?php

namespace WordPress\Blueprints\Steps\WriteFile;

use Playground\BaseStepInput;
use Playground\Resource;

class WriteFileStepInput extends BaseStepInput {
    public function __construct(
        public Resource $file,
        public string $toPath
    ) {}
}
