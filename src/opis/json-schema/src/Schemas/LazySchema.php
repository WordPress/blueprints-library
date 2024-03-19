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

namespace Opis\JsonSchema\Schemas;

use Opis\JsonSchema\{ValidationContext, Info\SchemaInfo, Schema};
use Opis\JsonSchema\Parsers\SchemaParser;
use Opis\JsonSchema\Errors\ValidationError;

final class LazySchema extends AbstractSchema {


	/**
	 * @var \Opis\JsonSchema\Parsers\SchemaParser
	 */
	private $parser;

	/**
	 * @var \Opis\JsonSchema\Schema|null
	 */
	private $schema;

	/**
	 * @param SchemaInfo   $info
	 * @param SchemaParser $parser
	 */
	public function __construct( SchemaInfo $info, SchemaParser $parser ) {
		parent::__construct( $info );
		$this->parser = $parser;
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\ValidationContext $context
	 */
	public function validate( $context ) {
		return $this->schema()->validate( $context );
	}

	/**
	 * @return Schema
	 */
	public function schema(): Schema {
		if ( $this->schema === null ) {
			$this->schema = $this->parser->parseSchema( $this->info );
		}

		return $this->schema;
	}
}
