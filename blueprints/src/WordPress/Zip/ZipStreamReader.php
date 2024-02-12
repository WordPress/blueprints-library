<?php

namespace WordPress\Zip;

class ZipStreamReader {

	const SIGNATURE_FILE = 0x04034b50;
	const SIGNATURE_CENTRAL_DIRECTORY = 0x02014b50;
	const SIGNATURE_CENTRAL_DIRECTORY_END = 0x06054b50;
	const COMPRESSION_DEFLATE = 8;
	const UINT32 = 'V';
	const UINT16 = 'v';

	/**
	 * Reads the next zip entry from a stream of zip file bytes.
	 *
	 * @param resource $fp A stream of zip file bytes.
	 */
	static public function readEntry( $fp ) {
		$signature = fread( $fp, 4 );
		if ( feof( $fp ) ) {
			return null;
		}
		$signature = unpack( 'V', $signature )[1];
		if ( $signature === static::SIGNATURE_FILE ) {
			return static::readFileEntry( $fp, true );
		} elseif ( $signature === static::SIGNATURE_CENTRAL_DIRECTORY ) {
			return static::readCentralDirectoryEntry( $fp, true );
		} elseif ( $signature === static::SIGNATURE_CENTRAL_DIRECTORY_END ) {
			return static::readEndCentralDirectoryEntry( $fp, true );
		}

		return null;
	}


	/**
	 * Reads a file entry from a zip file.
	 *
	 * The file entry is structured as follows:
	 *
	 * ```
	 * Offset    Bytes    Description
	 *   0        4    Local file header signature = 0x04034b50 (PK♥♦ or "PK\3\4")
	 *   4        2    Version needed to extract (minimum)
	 *   6        2    General purpose bit flag
	 *   8        2    Compression method; e.g. none = 0, DEFLATE = 8 (or "\0x08\0x00")
	 *   10        2    File last modification time
	 *   12        2    File last modification date
	 *   14        4    CRC-32 of uncompressed data
	 *   18        4    Compressed size (or 0xffffffff for ZIP64)
	 *   22        4    Uncompressed size (or 0xffffffff for ZIP64)
	 *   26        2    File name length (n)
	 *   28        2    Extra field length (m)
	 *   30        n    File name
	 *   30+n    m    Extra field
	 * ```
	 *
	 * @param resource $fp
	 */
	static protected function readFileEntry(
		$fp,
		bool $skipSignature = false
	) {
		if ( ! $skipSignature ) {
			$signature = fread( $fp, 4 );
			if ( feof( $fp ) ) {
				return null;
			}
			$signature = unpack( 'V', $signature )[1];
			if ( $signature !== static::SIGNATURE_FILE ) {
				return null;
			}
		}
		$entry                = [
			'signature'         => static::SIGNATURE_FILE,
			'version'           => unpack( static::UINT16, fread( $fp, 2 ) )[1],
			'generalPurpose'    => unpack( static::UINT16, fread( $fp, 2 ) )[1],
			'compressionMethod' => unpack( static::UINT16, fread( $fp, 2 ) )[1],
			'lastModifiedTime'  => unpack( static::UINT16, fread( $fp, 2 ) )[1],
			'lastModifiedDate'  => unpack( static::UINT16, fread( $fp, 2 ) )[1],
			'crc'               => unpack( static::UINT32, fread( $fp, 4 ) )[1],
			'compressedSize'    => unpack( static::UINT32, fread( $fp, 4 ) )[1],
			'uncompressedSize'  => unpack( static::UINT32, fread( $fp, 4 ) )[1],
		];
		$pathLength           = unpack( static::UINT16, fread( $fp, 2 ) )[1];
		$extraLength          = unpack( static::UINT16, fread( $fp, 2 ) )[1];
		$entry['path']        = fread( $fp, $pathLength );
		$entry['isDirectory'] = static::endsWithSlash( $entry['path'] );
		$entry['extra']       = fread( $fp, $extraLength );

		// Make sure we consume the body stream or else
		// we'll start reading the next file at the wrong
		// offset.
		// @TODO: Expose the body stream instead of reading it all
		//        eagerly. Ensure the next iteration exhausts
		//        the last body stream before moving on.
		$entry['bytes'] = fread( $fp, $entry['compressedSize'] );
		if ( $entry['compressionMethod'] === static::COMPRESSION_DEFLATE ) {
			$entry['bytes'] = gzinflate( $entry['bytes'] );
		}

		return $entry;
	}

	/**
	 * Reads a central directory entry from a zip file.
	 *
	 * The central directory entry is structured as follows:
	 *
	 * ```
	 * Offset Bytes Description
	 *   0        4    Central directory file header signature = 0x02014b50
	 *   4        2    Version made by
	 *   6        2    Version needed to extract (minimum)
	 *   8        2    General purpose bit flag
	 *   10        2    Compression method
	 *   12        2    File last modification time
	 *   14        2    File last modification date
	 *   16        4    CRC-32 of uncompressed data
	 *   20        4    Compressed size (or 0xffffffff for ZIP64)
	 *   24        4    Uncompressed size (or 0xffffffff for ZIP64)
	 *   28        2    File name length (n)
	 *   30        2    Extra field length (m)
	 *   32        2    File comment length (k)
	 *   34        2    Disk number where file starts (or 0xffff for ZIP64)
	 *   36        2    Internal file attributes
	 *   38        4    External file attributes
	 *   42        4    Relative offset of local file header (or 0xffffffff for ZIP64). This is the number of bytes between the start of the first disk on which the file occurs, and the start of the local file header. This allows software reading the central directory to locate the position of the file inside the ZIP file.
	 *   46        n    File name
	 *   46+n    m    Extra field
	 *   46+n+m    k    File comment
	 * ```
	 *
	 * @param resource stream
	 */
	static protected function readCentralDirectoryEntry(
		$stream,
		$skipSignature = false
	) {
		if ( ! $skipSignature ) {
			$signature = fread( $stream, 4 );
			if ( feof( $stream ) ) {
				return null;
			}
			$signature = unpack( 'V', $signature )[1];
			if ( $signature !== static::SIGNATURE_CENTRAL_DIRECTORY ) {
				return null;
			}
		}
		$data                 = fread( $stream, 42 );
		$data                 = unpack( 'vversionCreated/vversionNeeded/vgeneralPurpose/vcompressionMethod/vlastModifiedTime/vlastModifiedDate/Vcrc/VcompressedSize/VuncompressedSize/vpathLength/vextraLength/vfileCommentLength/vdiskNumber/vinternalAttributes/VexternalAttributes/VfirstByteAt',
			$data );
		$pathLength           = $data['pathLength'];
		$extraLength          = $data['extraLength'];
		$fileCommentLength    = $data['fileCommentLength'];
		$entry                = [
			'signature'          => static::SIGNATURE_CENTRAL_DIRECTORY,
			'versionCreated'     => $data['versionCreated'],
			'versionNeeded'      => $data['versionNeeded'],
			'generalPurpose'     => $data['generalPurpose'],
			'compressionMethod'  => $data['compressionMethod'],
			'lastModifiedTime'   => $data['lastModifiedTime'],
			'lastModifiedDate'   => $data['lastModifiedDate'],
			'crc'                => $data['crc'],
			'compressedSize'     => $data['compressedSize'],
			'uncompressedSize'   => $data['uncompressedSize'],
			'diskNumber'         => $data['diskNumber'],
			'internalAttributes' => $data['internalAttributes'],
			'externalAttributes' => $data['externalAttributes'],
			'firstByteAt'        => $data['firstByteAt'],
		];
		$entry['lastByteAt']  = $entry['firstByteAt'] + 30 + $pathLength + $fileCommentLength + $extraLength + $entry['compressedSize'] - 1;
		$entry['path']        = fread( $stream, $pathLength );
		$entry['isDirectory'] = static::endsWithSlash( $entry['path'] );

		$entry['extra']       = $extraLength ? fread( $stream, $extraLength ) : '';
		$entry['fileComment'] = $fileCommentLength ? fread( $stream, $fileCommentLength ) : '';

		return $entry;
	}

	static protected function endsWithSlash( string $path ) {
		return $path[ strlen( $path ) - 1 ] == ord( '/' );
	}

	/**
	 * Reads the end of central directory entry from a zip file.
	 *
	 * The end of central directory entry is structured as follows:
	 *
	 * ```
	 * Offset    Bytes    Description[33]
	 *   0         4        End of central directory signature = 0x06054b50
	 *   4         2        Number of this disk (or 0xffff for ZIP64)
	 *   6         2        Disk where central directory starts (or 0xffff for ZIP64)
	 *   8         2        Number of central directory records on this disk (or 0xffff for ZIP64)
	 *   10         2        Total number of central directory records (or 0xffff for ZIP64)
	 *   12         4        Size of central directory (bytes) (or 0xffffffff for ZIP64)
	 *   16         4        Offset of start of central directory, relative to start of archive (or 0xffffffff for ZIP64)
	 *   20         2        Comment length (n)
	 *   22         n        Comment
	 * ```
	 *
	 * @param resource $stream
	 */
	static protected function readEndCentralDirectoryEntry(
		$stream,
		bool $skipSignature = false
	) {
		if ( ! $skipSignature ) {
			$signature = fread( $stream, 4 );
			if ( feof( $stream ) ) {
				return null;
			}
			$signature = unpack( 'V', $signature )[1];
			if ( $signature !== static::SIGNATURE_CENTRAL_DIRECTORY_END ) {
				return null;
			}
		}
		$data             = fread( $stream, 18 );
		$data             = unpack( 'vdiskNumber/vcentralDirectoryStartDisk/vnumberCentralDirectoryRecordsOnThisDisk/vnumberCentralDirectoryRecords/VcentralDirectorySize/VcentralDirectoryOffset/vcommentLength',
			$data );
		$commentLength    = $data['commentLength'];
		$entry            = [
			'signature'                               => static::SIGNATURE_CENTRAL_DIRECTORY_END,
			'diskNumber'                              => $data['diskNumber'],
			'centralDirectoryStartDisk'               => $data['centralDirectoryStartDisk'],
			'numberCentralDirectoryRecordsOnThisDisk' => $data['numberCentralDirectoryRecordsOnThisDisk'],
			'numberCentralDirectoryRecords'           => $data['numberCentralDirectoryRecords'],
			'centralDirectorySize'                    => $data['centralDirectorySize'],
			'centralDirectoryOffset'                  => $data['centralDirectoryOffset'],
		];
		$entry['comment'] = $commentLength ? fread( $stream, $commentLength ) : '';

		return $entry;
	}

}
