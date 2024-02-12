<?php

namespace WordPress\Blueprints\Steps\Mkdir;

use WordPress\Blueprints\Steps\BaseStep;

class MkdirStep extends BaseStep {
    public function __construct(
        private MkdirStepInput $input
    ) {}

    public function execute() {
        $success = mkdir($this->input->path);
        if (!$success) {
            throw new \Exception("Failed to create directory at {$this->input->path}");
        }
    }
}
