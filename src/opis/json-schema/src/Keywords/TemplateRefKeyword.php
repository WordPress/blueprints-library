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

use Opis\Uri\UriTemplate;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Exceptions\UnresolvedReferenceException;
use Opis\JsonSchema\{JsonPointer, Schema, SchemaLoader, Uri, ValidationContext, Variables};

class TemplateRefKeyword extends AbstractRefKeyword {

	/**
	 * @var \Opis\Uri\UriTemplate
	 */
	protected $template;
	/**
	 * @var \Opis\JsonSchema\Variables|null
	 */
	protected $vars;
	/** @var Schema[]|null[] */
	protected $cached = array();
	/**
	 * @var bool
	 */
	protected $allowRelativeJsonPointerInRef;

	public function __construct(
		UriTemplate $template,
		$vars,
		$mapper = null,
		$globals = null,
		$slots = null,
		string $keyword = '$ref',
		bool $allowRelativeJsonPointerInRef = true
	) {
		parent::__construct( $mapper, $globals, $slots, $keyword );
		$this->template                      = $template;
		$this->vars                          = $vars;
		$this->allowRelativeJsonPointerInRef = $allowRelativeJsonPointerInRef;
	}

	/**
	 * @param \Opis\JsonSchema\ValidationContext $context
	 * @param \Opis\JsonSchema\Schema            $schema
	 */
	protected function doValidate( $context, $schema ) {
		if ( $this->vars ) {
			$vars = $this->vars->resolve( $context->rootData(), $context->currentDataPath() );
			if ( ! is_array( $vars ) ) {
				$vars = (array) $vars;
			}
			$vars += $context->globals();
		} else {
			$vars = $context->globals();
		}

		$ref = $this->template->resolve( $vars );

		$key = isset( $ref[32] ) ? md5( $ref ) : $ref;

		if ( ! array_key_exists( $key, $this->cached ) ) {
			$this->cached[ $key ] = $this->resolveRef( $ref, $context->loader(), $schema );
		}

		$resolved = $this->cached[ $key ];
		unset( $key );

		if ( ! $resolved ) {
			throw new UnresolvedReferenceException( $ref, $schema, $context );
		}

		return $resolved->validate( $this->createContext( $context, $schema ) );
	}

	/**
	 * @param string       $ref
	 * @param SchemaLoader $repo
	 * @param Schema       $schema
	 * @return null|Schema
	 */
	protected function resolveRef( $ref, $repo, $schema ) {
		if ( $ref === '' ) {
			return null;
		}

		$baseUri = $schema->info()->idBaseRoot();

		if ( $ref === '#' ) {
			return $repo->loadSchemaById( $baseUri );
		}

		// Check if is pointer
		if ( $ref[0] === '#' ) {
			if ( $pointer = JsonPointer::parse( substr( $ref, 1 ) ) ) {
				if ( $pointer->isAbsolute() ) {
					return $this->resolvePointer( $repo, $pointer, $baseUri );
				}
				unset( $pointer );
			}
		} elseif ( $this->allowRelativeJsonPointerInRef && ( $pointer = JsonPointer::parse( $ref ) ) ) {
			if ( $pointer->isRelative() ) {
				return $this->resolvePointer( $repo, $pointer, $baseUri, $schema->info()->path() );
			}
			unset( $pointer );
		}

		$ref = Uri::merge( $ref, $baseUri, true );

		if ( $ref === null || ! $ref->isAbsolute() ) {
			return null;
		}

		return $repo->loadSchemaById( $ref );
	}
}
