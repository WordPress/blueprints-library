<?php

namespace WordPress\Zip;

class ZipFileEntry {
	public readonly bool $isDirectory;

	public function __construct(
		public readonly int $version,
		public readonly int $generalPurpose,
		public readonly int $compressionMethod,
		public readonly int $lastModifiedTime,
		public readonly int $lastModifiedDate,
		public readonly int $crc,
		public readonly int $compressedSize,
		public readonly int $uncompressedSize,
		public readonly string $path,
		public readonly string $extra,
		public readonly string $bytes
	) {
		$this->isDirectory = $this->path[ - 1 ] === '/';
	}
	
	public function isFileEntry() {
		return true;
	}

}
