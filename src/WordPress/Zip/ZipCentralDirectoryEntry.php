<?php

namespace WordPress\Zip;

class ZipCentralDirectoryEntry {

	public bool $isDirectory;
	public int $firstByteAt;
	public int $versionCreated;
	public int $versionNeeded;
	public int $generalPurpose;
	public int $compressionMethod;
	public int $lastModifiedTime;
	public int $lastModifiedDate;
	public int $crc;
	public int $compressedSize;
	public int $uncompressedSize;
	public int $diskNumber;
	public int $internalAttributes;
	public int $externalAttributes;
	public int $lastByteAt;
	public string $path;
	public string $extra;
	public string $fileComment;

	public function __construct(
		int $versionCreated,
		int $versionNeeded,
		int $generalPurpose,
		int $compressionMethod,
		int $lastModifiedTime,
		int $lastModifiedDate,
		int $crc,
		int $compressedSize,
		int $uncompressedSize,
		int $diskNumber,
		int $internalAttributes,
		int $externalAttributes,
		int $firstByteAt,
		int $lastByteAt,
		string $path,
		string $extra,
		string $fileComment
	) {
		$this->fileComment        = $fileComment;
		$this->extra              = $extra;
		$this->path               = $path;
		$this->lastByteAt         = $lastByteAt;
		$this->externalAttributes = $externalAttributes;
		$this->internalAttributes = $internalAttributes;
		$this->diskNumber         = $diskNumber;
		$this->uncompressedSize   = $uncompressedSize;
		$this->compressedSize     = $compressedSize;
		$this->crc                = $crc;
		$this->lastModifiedDate   = $lastModifiedDate;
		$this->lastModifiedTime   = $lastModifiedTime;
		$this->compressionMethod  = $compressionMethod;
		$this->generalPurpose     = $generalPurpose;
		$this->versionNeeded      = $versionNeeded;
		$this->versionCreated     = $versionCreated;
		$this->firstByteAt        = $firstByteAt;
		$this->isDirectory        = $this->path[- 1] === '/';
	}

	public function isFileEntry() {
		return false;
	}
}
