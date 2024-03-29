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

namespace Opis\JsonSchema\Parsers\Keywords;

use Opis\JsonSchema\Keyword;
use Opis\JsonSchema\Info\SchemaInfo;
use Opis\JsonSchema\Keywords\AllOfKeyword;
use Opis\JsonSchema\Parsers\{KeywordParser, SchemaParser};

class AllOfKeywordParser extends KeywordParser {

	/**
	 * @inheritDoc
	 */
	public function type(): string {
		return self::TYPE_AFTER;
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\Info\SchemaInfo      $info
	 * @param \Opis\JsonSchema\Parsers\SchemaParser $parser
	 * @param object                                $shared
	 */
	public function parse( $info, $parser, $shared ) {
		$schema = $info->data();

		if ( ! $this->keywordExists( $schema ) ) {
			return null;
		}

		$value = $this->keywordValue( $schema );

		if ( ! is_array( $value ) ) {
			throw $this->keywordException( '{keyword} should be an array of json schemas', $info );
		}

		if ( ! $value ) {
			throw $this->keywordException( '{keyword} must have at least one element', $info );
		}

		$valid = 0;

		foreach ( $value as $index => $item ) {
			if ( $item === false ) {
				throw $this->keywordException( '{keyword} contains false schema', $info );
			}
			if ( $item === true ) {
				++$valid;
				continue;
			}
			if ( ! is_object( $item ) ) {
				throw $this->keywordException( "{keyword}[{$index}] must be a json schema", $info );
			} elseif ( ! count( get_object_vars( $item ) ) ) {
				++$valid;
			}
		}

		return $valid !== count( $value ) ? new AllOfKeyword( $value ) : null;
	}
}
