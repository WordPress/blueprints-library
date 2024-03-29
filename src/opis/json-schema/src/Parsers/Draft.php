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

namespace Opis\JsonSchema\Parsers;

abstract class Draft extends Vocabulary {

	/**
	 * @param Vocabulary|null $extraVocabulary
	 */
	public function __construct( $extraVocabulary = null ) {
		$keywords          = $this->getKeywordParsers();
		$keywordValidators = $this->getKeywordValidatorParsers();
		$pragmas           = $this->getPragmaParsers();

		if ( $extraVocabulary ) {
			$keywords          = array_merge( $keywords, $extraVocabulary->keywords() );
			$keywordValidators = array_merge( $keywordValidators, $extraVocabulary->keywordValidators() );
			$pragmas           = array_merge( $pragmas, $extraVocabulary->pragmas() );
		}

		array_unshift( $keywords, $this->getRefKeywordParser() );

		parent::__construct( $keywords, $keywordValidators, $pragmas );
	}

	/**
	 * @return string
	 */
	abstract public function version(): string;

	/**
	 * @return bool
	 */
	abstract public function allowKeywordsAlongsideRef(): bool;

	/**
	 * @return bool
	 */
	abstract public function supportsAnchorId(): bool;

	/**
	 * @return KeywordParser
	 */
	abstract protected function getRefKeywordParser(): KeywordParser;

	/**
	 * @return KeywordParser[]
	 */
	abstract protected function getKeywordParsers(): array;

	/**
	 * @return KeywordValidatorParser[]
	 */
	protected function getKeywordValidatorParsers(): array {
		return array();
	}

	/**
	 * @return PragmaParser[]
	 */
	protected function getPragmaParsers(): array {
		return array();
	}
}
