<?php

namespace WordPress\Streams;

class AsyncStreamWrapper extends VanillaStreamWrapper {
	const SCHEME = 'async';
	/** @var StreamPollingGroup */
	private $group;

	public function stream_open( $path, $mode, $options, &$opened_path ) {
		if ( ! parent::stream_open( $path, $mode, $options, $opened_path ) ) {
			return false;
		}

		if ( ! $this->wrapper_data->group ) {
			return false;
		}
		$this->group = $this->wrapper_data->group;

		return true;
	}

	public function stream_read( $count ) {
		return $this->group->read_bytes( $this->stream, $count );
	}

}
