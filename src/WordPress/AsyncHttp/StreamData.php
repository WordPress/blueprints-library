<?php

namespace WordPress\AsyncHttp;

use WordPress\Streams\VanillaStreamWrapperData;

class StreamData extends VanillaStreamWrapperData {

	public Request $request;
	public Client $client;

	public function __construct( Request $request, Client $group ) {
		parent::__construct( null );
		$this->request = $request;
		$this->client = $group;
	}

}
