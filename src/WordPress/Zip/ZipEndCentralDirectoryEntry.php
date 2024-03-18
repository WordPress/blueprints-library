<?php

namespace WordPress\Zip;

class ZipEndCentralDirectoryEntry {

	public int $diskNumber;
	public int $centralDirectoryStartDisk;
	public int $numberCentralDirectoryRecordsOnThisDisk;
	public int $numberCentralDirectoryRecords;
	public int $centralDirectorySize;
	public int $centralDirectoryOffset;
	public string $comment;

	public function __construct(
		int $diskNumber,
		int $centralDirectoryStartDisk,
		int $numberCentralDirectoryRecordsOnThisDisk,
		int $numberCentralDirectoryRecords,
		int $centralDirectorySize,
		int $centralDirectoryOffset,
		string $comment
	) {
		$this->comment                                 = $comment;
		$this->centralDirectoryOffset                  = $centralDirectoryOffset;
		$this->centralDirectorySize                    = $centralDirectorySize;
		$this->numberCentralDirectoryRecords           = $numberCentralDirectoryRecords;
		$this->numberCentralDirectoryRecordsOnThisDisk = $numberCentralDirectoryRecordsOnThisDisk;
		$this->centralDirectoryStartDisk               = $centralDirectoryStartDisk;
		$this->diskNumber                              = $diskNumber;
	}

	public function isFileEntry() {
		return false;
	}
}
