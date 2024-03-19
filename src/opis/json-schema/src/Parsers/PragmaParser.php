<?php
/*
===========================================================================
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

namespace Opis\JsonSchema\Parsers;

use Opis\JsonSchema\Info\SchemaInfo;
use Opis\JsonSchema\Exceptions\InvalidPragmaException;
use Opis\JsonSchema\Pragma;

abstract class PragmaParser {

	/**
	 * @var string
	 */
	protected $pragma;

	/**
	 * @param string $pragma
	 */
	public function __construct( string $pragma ) {
		$this->pragma = $pragma;
	}

	/**
	 * @param SchemaInfo   $info
	 * @param SchemaParser $parser
	 * @param object       $shared
	 * @return Pragma|null
	 */
	abstract public function parse( $info, $parser, $shared );

	/**
	 * @param object|SchemaInfo $schema
	 * @param string|null       $pragma
	 * @return bool
	 */
	protected function pragmaExists( $schema, $pragma = null ): bool {
		if ( $schema instanceof SchemaInfo ) {
			$schema = $schema->isObject() ? $schema->data() : null;
		}

		return is_object( $schema ) && property_exists( $schema, $pragma ?? $this->pragma );
	}

	/**
	 * @param object|SchemaInfo $schema
	 * @param string|null       $pragma
	 * @return mixed
	 */
	protected function pragmaValue( $schema, $pragma = null ) {
		if ( $schema instanceof SchemaInfo ) {
			$schema = $schema->isObject() ? $schema->data() : null;
		}

		return is_object( $schema ) ? $schema->{$pragma ?? $this->pragma} : null;
	}

	/**
	 * @param string      $message
	 * @param SchemaInfo  $info
	 * @param string|null $pragma
	 * @return InvalidPragmaException
	 */
	protected function pragmaException( $message, $info, $pragma = null ): InvalidPragmaException {
		$pragma = $pragma ?? $this->pragma;

		return new InvalidPragmaException( str_replace( '{pragma}', $pragma, $message ), $pragma, $info );
	}
}
