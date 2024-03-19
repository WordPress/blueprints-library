<?php
/*
============================================================================
 * Copyright 2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\JsonSchema\KeywordValidators;

use Opis\JsonSchema\{ValidationContext, KeywordValidator};
use Opis\JsonSchema\Errors\ValidationError;

final class CallbackKeywordValidator implements KeywordValidator {

	/** @var callable */
	private $callback;

	/**
	 * @param callable $callback
	 */
	public function __construct( callable $callback ) {
		$this->callback = $callback;
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\ValidationContext $context
	 */
	public function validate( $context ) {
		return ( $this->callback )( $context );
	}

	/**
	 * @inheritDoc
	 */
	public function next() {
		return null;
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\KeywordValidator|null $next
	 */
	public function setNext( $next ): KeywordValidator {
		return $this;
	}
}
