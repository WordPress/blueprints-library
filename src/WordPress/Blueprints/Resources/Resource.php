<?php

namespace WordPress\Blueprints\Resources;

interface Resource {
	public function saveTo( string $path ): void;

	public function read( int $bytes ): string|bool;

	public function close(): void;
}
