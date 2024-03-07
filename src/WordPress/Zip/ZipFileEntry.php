<?php

namespace WordPress\Zip;

class ZipFileEntry {
	public bool $isDirectory;
	public int $version;
	public int $generalPurpose;
	public int $compressionMethod;
	public int $lastModifiedTime;
	public int $lastModifiedDate;
	public int $crc;
	public int $compressedSize;
	public int $uncompressedSize;
	public string $path;
	public string $extra;
	public string $bytes;

	public function __construct(
		int $version,
		int $generalPurpose,
		int $compressionMethod,
		int $lastModifiedTime,
		int $lastModifiedDate,
		int $crc,
		int $compressedSize,
		int $uncompressedSize,
		string $path,
		string $extra,
		string $bytes
	) {
		$this->bytes             = $bytes;
		$this->extra             = $extra;
		$this->path              = $path;
		$this->uncompressedSize  = $uncompressedSize;
		$this->compressedSize    = $compressedSize;
		$this->crc               = $crc;
		$this->lastModifiedDate  = $lastModifiedDate;
		$this->lastModifiedTime  = $lastModifiedTime;
		$this->compressionMethod = $compressionMethod;
		$this->generalPurpose    = $generalPurpose;
		$this->version           = $version;
		$this->isDirectory       = $this->path[- 1] === '/';
	}

	public function isFileEntry() {
		return true;
	}
}
