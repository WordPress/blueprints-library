<?php

namespace WordPress\Streams;

class AsyncHttpStreamData extends VanillaStreamWrapperData {

	public Request $request;
	public AsyncHttpClient $client;

	public function __construct( Request $request, AsyncHttpClient $group ) {
		parent::__construct( null );
		$this->request = $request;
		$this->client = $group;
	}

}
