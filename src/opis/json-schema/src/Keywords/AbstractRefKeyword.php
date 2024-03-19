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

namespace Opis\JsonSchema\Keywords;

use Opis\JsonSchema\{Errors\ValidationError,
	JsonPointer,
	Keyword,
	Schema,
	SchemaLoader,
	Uri,
	ValidationContext,
	Variables};

abstract class AbstractRefKeyword implements Keyword {

	use ErrorTrait;

	/**
	 * @var string
	 */
	protected $keyword;
	/**
	 * @var \Opis\JsonSchema\Variables|null
	 */
	protected $mapper;
	/**
	 * @var \Opis\JsonSchema\Variables|null
	 */
	protected $globals;
	/**
	 * @var mixed[]|null
	 */
	protected $slots;
	/**
	 * @var \Opis\JsonSchema\Uri|null
	 */
	protected $lastRefUri;

	/**
	 * @param Variables|null $mapper
	 * @param Variables|null $globals
	 * @param array|null     $slots
	 * @param string         $keyword
	 */
	protected function __construct( $mapper, $globals, $slots = null, string $keyword = '$ref' ) {
		$this->mapper  = $mapper;
		$this->globals = $globals;
		$this->slots   = $slots;
		$this->keyword = $keyword;
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\ValidationContext $context
	 * @param \Opis\JsonSchema\Schema            $schema
	 */
	public function validate( $context, $schema ) {
		if ( $error = $this->doValidate( $context, $schema ) ) {
			$uri              = $this->lastRefUri;
			$this->lastRefUri = null;

			return $this->error(
				$schema,
				$context,
				$this->keyword,
				'The data must match {keyword}',
				array(
					'keyword' => $this->keyword,
					'uri'     => (string) $uri,
				),
				$error
			);
		}

		$this->lastRefUri = null;

		return null;
	}


	/**
	 * @param \Opis\JsonSchema\ValidationContext $context
	 * @param \Opis\JsonSchema\Schema            $schema
	 */
	abstract protected function doValidate( $context, $schema );

	/**
	 * @param \Opis\JsonSchema\Uri|null $uri
	 */
	protected function setLastRefUri( $uri ) {
		$this->lastRefUri = $uri;
	}

	/**
	 * @param \Opis\JsonSchema\Schema $schema
	 */
	protected function setLastRefSchema( $schema ) {
		$info = $schema->info();

		if ( $info->id() ) {
			$this->lastRefUri = $info->id();
		} else {
			$this->lastRefUri = Uri::merge( JsonPointer::pathToFragment( $info->path() ), $info->idBaseRoot() );
		}
	}

	/**
	 * @param ValidationContext $context
	 * @param Schema            $schema
	 * @return ValidationContext
	 */
	protected function createContext( $context, $schema ): ValidationContext {
		return $context->create( $schema, $this->mapper, $this->globals, $this->slots );
	}

	/**
	 * @param SchemaLoader $repo
	 * @param JsonPointer  $pointer
	 * @param Uri          $base
	 * @param array|null   $path
	 * @return null|Schema
	 */
	protected function resolvePointer(
		$repo,
		$pointer,
		$base,
		$path = null
	) {
		if ( $pointer->isAbsolute() ) {
			$path = (string) $pointer;
		} else {
			if ( $pointer->hasFragment() ) {
				return null;
			}

			$path = $path ? $pointer->absolutePath( $path ) : $pointer->path();
			if ( $path === null ) {
				return null;
			}

			$path = JsonPointer::pathToString( $path );
		}

		return $repo->loadSchemaById( Uri::merge( '#' . $path, $base ) );
	}
}
