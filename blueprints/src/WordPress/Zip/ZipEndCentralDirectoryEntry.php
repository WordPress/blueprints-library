<?php

namespace WordPress\Zip;

class ZipEndCentralDirectoryEntry {

	public function __construct(
		public readonly int $diskNumber,
		public readonly int $centralDirectoryStartDisk,
		public readonly int $numberCentralDirectoryRecordsOnThisDisk,
		public readonly int $numberCentralDirectoryRecords,
		public readonly int $centralDirectorySize,
		public readonly int $centralDirectoryOffset,
		public readonly string $comment
	) {
	}
}
