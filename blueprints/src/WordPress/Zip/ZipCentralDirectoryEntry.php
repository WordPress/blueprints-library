<?php

namespace WordPress\Zip;

class ZipCentralDirectoryEntry {

	public readonly bool $isDirectory;

	public function __construct(
		public readonly int $versionCreated,
		public readonly int $versionNeeded,
		public readonly int $generalPurpose,
		public readonly int $compressionMethod,
		public readonly int $lastModifiedTime,
		public readonly int $lastModifiedDate,
		public readonly int $crc,
		public readonly int $compressedSize,
		public readonly int $uncompressedSize,
		public readonly int $diskNumber,
		public readonly int $internalAttributes,
		public readonly int $externalAttributes,
		public readonly int $firstByteAt,
		public readonly int $lastByteAt,
		public readonly string $path,
		public readonly string $extra,
		public readonly string $fileComment,
	) {
		$this->isDirectory = $this->path[ - 1 ] === '/';
	}

}
