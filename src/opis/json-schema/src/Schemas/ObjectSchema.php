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

use Opis\JsonSchema\{Helper, Keyword, ValidationContext, KeywordValidator};
use Opis\JsonSchema\Info\SchemaInfo;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\KeywordValidators\CallbackKeywordValidator;

class ObjectSchema extends AbstractSchema {

	/**
	 * @var \Opis\JsonSchema\KeywordValidator|null
	 */
	protected $keywordValidator;

	/** @var Keyword[]|null */
	protected $before;

	/** @var Keyword[]|null */
	protected $after;

	/** @var Keyword[][]|null */
	protected $types;

	/**
	 * @param SchemaInfo            $info
	 * @param KeywordValidator|null $keywordValidator
	 * @param Keyword[][]|null      $types
	 * @param Keyword[]|null        $before
	 * @param Keyword[]|null        $after
	 */
	public function __construct( SchemaInfo $info, $keywordValidator, $types, $before, $after ) {
		parent::__construct( $info );
		$this->types            = $types;
		$this->before           = $before;
		$this->after            = $after;
		$this->keywordValidator = $keywordValidator;

		if ( $keywordValidator ) {
			while ( $next = $keywordValidator->next() ) {
				$keywordValidator = $next;
			}
			$keywordValidator->setNext( new CallbackKeywordValidator( array( $this, 'doValidate' ) ) );
		}
	}

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\ValidationContext $context
	 */
	public function validate( $context ) {
		$context->pushSharedObject( $this );
		$error = $this->keywordValidator ? $this->keywordValidator->validate( $context ) : $this->doValidate( $context );
		$context->popSharedObject();

		return $error;
	}

	/**
	 * @param ValidationContext $context
	 * @return null|ValidationError
	 * @internal
	 */
	public function doValidate( $context ) {
		if ( $this->before && ( $error = $this->applyKeywords( $this->before, $context ) ) ) {
			return $error;
		}

		if ( $this->types && ( $type = $context->currentDataType() ) ) {
			if ( isset( $this->types[ $type ] ) && ( $error = $this->applyKeywords( $this->types[ $type ], $context ) ) ) {
				return $error;
			}

			if ( ( $type = Helper::getJsonSuperType( $type ) ) && isset( $this->types[ $type ] ) ) {
				if ( $error = $this->applyKeywords( $this->types[ $type ], $context ) ) {
					return $error;
				}
			}

			unset( $type );
		}

		if ( $this->after && ( $error = $this->applyKeywords( $this->after, $context ) ) ) {
			return $error;
		}

		return null;
	}

	/**
	 * @param Keyword[]         $keywords
	 * @param ValidationContext $context
	 * @return ValidationError|null
	 */
	protected function applyKeywords( $keywords, $context ) {
		foreach ( $keywords as $keyword ) {
			if ( $error = $keyword->validate( $context, $this ) ) {
				return $error;
			}
		}

		return null;
	}
}
