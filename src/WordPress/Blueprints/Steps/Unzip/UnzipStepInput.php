<?php


namespace WordPress\Blueprints\Steps\Unzip;

use WordPress\Blueprints\Steps\BaseStepInput;
use WordPress\Blueprints\Resources\Resource;

class UnzipStepInput extends BaseStepInput {
    public function __construct(
        public Resource $zipFile,
        public string $toPath
    ) {}
}
