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

use Opis\JsonSchema\{
	Keyword, KeywordValidator, Schema, Uri
};
use Opis\JsonSchema\Schemas\{
	BooleanSchema, EmptySchema, ExceptionSchema, ObjectSchema
};
use Opis\JsonSchema\Resolvers\{
	FilterResolver,
	FormatResolver,
	ContentMediaTypeResolver,
	ContentEncodingResolver
};
use Opis\JsonSchema\Parsers\Drafts\{Draft06, Draft07, Draft201909, Draft202012};
use Opis\JsonSchema\Exceptions\{ParseException, SchemaException};
use Opis\JsonSchema\Info\SchemaInfo;

class SchemaParser {

	const DRAFT_REGEX   = '~^https?://json-schema\.org/draft(?:/|-)(\d[0-9-]*\d)/schema#?$~i';
	const ANCHOR_REGEX  = '/^[a-z][a-z0-9\\-.:_]*/i';
	const DEFAULT_DRAFT = '2020-12';

	/** @var array */
	const DEFAULT_OPTIONS = array(
		'allowFilters'                  => true,
		'allowFormats'                  => true,
		'allowMappers'                  => true,
		'allowTemplates'                => true,
		'allowGlobals'                  => true,
		'allowDefaults'                 => true,
		'allowSlots'                    => true,
		'allowKeywordValidators'        => true,
		'allowPragmas'                  => true,

		'allowDataKeyword'              => true,
		'allowKeywordsAlongsideRef'     => false,
		'allowUnevaluated'              => true,
		'allowRelativeJsonPointerInRef' => true,
		'allowExclusiveMinMaxAsBool'    => true,

		'keepDependenciesKeyword'       => true,
		'keepAdditionalItemsKeyword'    => true,

		'decodeContent'                 => array( '06', '07' ),
		'defaultDraft'                  => self::DEFAULT_DRAFT,

		'varRefKey'                     => '$ref',
		'varEachKey'                    => '$each',
		'varDefaultKey'                 => 'default',
	);

	/** @var array */
	protected $options;

	/** @var Draft[] */
	protected $drafts;

	/** @var array */
	protected $resolvers;

	/**
	 * @param array           $resolvers
	 * @param array           $options
	 * @param Vocabulary|null $extraVocabulary
	 */
	public function __construct(
		array $resolvers = array(),
		array $options = array(),
		$extraVocabulary = null
	) {
		if ( $options ) {
			$this->options = $options + self::DEFAULT_OPTIONS;
		} else {
			$this->options = self::DEFAULT_OPTIONS;
		}

		$this->resolvers = $this->getResolvers( $resolvers );

		$this->drafts = $this->getDrafts( $extraVocabulary ?? new DefaultVocabulary() );
	}

	/**
	 * @param Vocabulary|null $extraVocabulary
	 * @return array
	 */
	protected function getDrafts( $extraVocabulary ): array {
		return array(
			'06'      => new Draft06( $extraVocabulary ),
			'07'      => new Draft07( $extraVocabulary ),
			'2019-09' => new Draft201909( $extraVocabulary ),
			'2020-12' => new Draft202012( $extraVocabulary ),
		);
	}

	/**
	 * @param array $resolvers
	 * @return array
	 */
	protected function getResolvers( $resolvers ): array {
		if ( ! array_key_exists( 'format', $resolvers ) ) {
			$resolvers['format'] = new FormatResolver();
		}

		if ( ! array_key_exists( 'contentEncoding', $resolvers ) ) {
			$resolvers['contentEncoding'] = new ContentEncodingResolver();
		}

		if ( ! array_key_exists( 'contentMediaType', $resolvers ) ) {
			$resolvers['contentMediaType'] = new ContentMediaTypeResolver();
		}

		if ( ! array_key_exists( '$filters', $resolvers ) ) {
			$resolvers['$filters'] = new FilterResolver();
		}

		return $resolvers;
	}

	/**
	 * @param string $name
	 * @param null   $default
	 * @return mixed|null
	 */
	public function option( $name, $default = null ) {
		return $this->options[ $name ] ?? $default;
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return $this
	 */
	public function setOption( $name, $value ): self {
		$this->options[ $name ] = $value;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array {
		return $this->options;
	}

	/**
	 * @param string   $name
	 * @param $resolver
	 * @return $this
	 */
	public function setResolver( $name, $resolver ): self {
		$this->resolvers[ $name ] = $resolver;

		return $this;
	}

	/**
	 * @return null|FilterResolver
	 */
	public function getFilterResolver() {
		return $this->getResolver( '$filters' );
	}

	/**
	 * @param null|FilterResolver $resolver
	 * @return $this
	 */
	public function setFilterResolver( $resolver ): self {
		return $this->setResolver( '$filters', $resolver );
	}

	/**
	 * @return null|FormatResolver
	 */
	public function getFormatResolver() {
		return $this->getResolver( 'format' );
	}

	/**
	 * @param FormatResolver|null $resolver
	 * @return $this
	 */
	public function setFormatResolver( $resolver ): self {
		return $this->setResolver( 'format', $resolver );
	}

	/**
	 * @return null|ContentEncodingResolver
	 */
	public function getContentEncodingResolver() {
		return $this->getResolver( 'contentEncoding' );
	}

	/**
	 * @param ContentEncodingResolver|null $resolver
	 * @return $this
	 */
	public function setContentEncodingResolver( $resolver ): self {
		return $this->setResolver( 'contentEncoding', $resolver );
	}

	/**
	 * @return null|ContentMediaTypeResolver
	 */
	public function getMediaTypeResolver() {
		return $this->getResolver( 'contentMediaType' );
	}

	/**
	 * @param ContentMediaTypeResolver|null $resolver
	 * @return $this
	 */
	public function setMediaTypeResolver( $resolver ): self {
		return $this->setResolver( 'contentMediaType', $resolver );
	}

	/**
	 * @return string
	 */
	public function defaultDraftVersion(): string {
		return $this->option( 'defaultDraft', self::DEFAULT_DRAFT );
	}

	/**
	 * @param string $draft
	 * @return $this
	 */
	public function setDefaultDraftVersion( $draft ): self {
		return $this->setOption( 'defaultDraft', $draft );
	}

	/**
	 * @param string $schema
	 * @return string|null
	 */
	public function parseDraftVersion( $schema ) {
		if ( ! preg_match( self::DRAFT_REGEX, $schema, $m ) ) {
			return null;
		}

		return $m[1] ?? null;
	}

	/**
	 * @param object $schema
	 * @return string|null
	 */
	public function parseId( $schema ) {
		if ( property_exists( $schema, '$id' ) && is_string( $schema->{'$id'} ) ) {
			return $schema->{'$id'};
		}

		return null;
	}

	/**
	 * @param object $schema
	 * @param string $draft
	 * @return string|null
	 */
	public function parseAnchor( $schema, $draft ) {
		if ( ! property_exists( $schema, '$anchor' ) ||
			! isset( $this->drafts[ $draft ] ) ||
			! $this->drafts[ $draft ]->supportsAnchorId() ) {
			return null;
		}

		$anchor = $schema->{'$anchor'};

		if ( ! is_string( $anchor ) || ! preg_match( self::ANCHOR_REGEX, $anchor ) ) {
			return null;
		}

		return $anchor;
	}

	/**
	 * @param object $schema
	 * @return string|null
	 */
	public function parseSchemaDraft( $schema ) {
		if ( ! property_exists( $schema, '$schema' ) || ! is_string( $schema->{'$schema'} ) ) {
			return null;
		}

		return $this->parseDraftVersion( $schema->{'$schema'} );
	}

	/**
	 * @param object      $schema
	 * @param Uri         $id
	 * @param callable    $handle_id
	 * @param callable    $handle_object
	 * @param string|null $draft
	 * @return Schema|null
	 */
	public function parseRootSchema(
		$schema,
		$id,
		$handle_id,
		$handle_object,
		$draft = null
	) {
		$existent = false;
		if ( property_exists( $schema, '$id' ) ) {
			$existent = true;
			$id       = Uri::parse( $schema->{'$id'}, true );
		}

		if ( $id instanceof Uri ) {
			if ( $id->fragment() === null ) {
				$id = Uri::merge( $id, null, true );
			}
		} else {
			throw new ParseException( 'Root schema id must be an URI', new SchemaInfo( $schema, $id ) );
		}

		if ( ! $id->isAbsolute() ) {
			throw new ParseException( 'Root schema id must be an absolute URI', new SchemaInfo( $schema, $id ) );
		}

		if ( $id->fragment() !== '' ) {
			throw new ParseException( 'Root schema id must have an empty fragment or none', new SchemaInfo( $schema, $id ) );
		}

		// Check if id exists
		if ( $resolved = $handle_id( $id ) ) {
			return $resolved;
		}

		if ( property_exists( $schema, '$schema' ) ) {
			if ( ! is_string( $schema->{'$schema'} ) ) {
				throw new ParseException( 'Schema draft must be a string', new SchemaInfo( $schema, $id ) );
			}
			$draft = $this->parseDraftVersion( $schema->{'$schema'} );
		}

		if ( $draft === null ) {
			$draft = $this->defaultDraftVersion();
		}

		if ( ! $existent ) {
			$schema->{'$id'} = (string) $id;
		}

		$resolved = $handle_object( $schema, $id, $draft );

		if ( ! $existent ) {
			unset( $schema->{'$id'} );
		}

		return $resolved;
	}

	/**
	 * @param SchemaInfo $info
	 * @return Schema
	 */
	public function parseSchema( $info ): Schema {
		if ( $info->isBoolean() ) {
			return new BooleanSchema( $info );
		}

		try {
			return $this->parseSchemaObject( $info );
		} catch ( SchemaException $exception ) {
			return new ExceptionSchema( $info, $exception );
		}
	}

	/**
	 * @param string $version
	 * @return Draft|null
	 */
	public function draft( $version ) {
		return $this->drafts[ $version ] ?? null;
	}

	/**
	 * @param Draft $draft
	 * @return $this
	 */
	public function addDraft( $draft ): self {
		$this->drafts[ $draft->version() ] = $draft;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function supportedDrafts(): array {
		return array_keys( $this->drafts );
	}

	/**
	 * @param array $options
	 * @return $this
	 */
	protected function setOptions( $options ): self {
		$this->options = $options + $this->options;

		return $this;
	}

	/**
	 * @param string $name
	 * @return mixed|null
	 */
	protected function getResolver( $name ) {
		$resolver = $this->resolvers[ $name ] ?? null;

		if ( ! is_object( $resolver ) ) {
			return null;
		}

		return $resolver;
	}

	/**
	 * @param SchemaInfo $info
	 * @return Schema
	 */
	protected function parseSchemaObject( $info ): Schema {
		$draftObject = $this->draft( $info->draft() );

		if ( $draftObject === null ) {
			throw new ParseException( "Unsupported draft-{$info->draft()}", $info );
		}

		/** @var object $schema */
		$schema = $info->data();

		// Check id
		if ( property_exists( $schema, '$id' ) ) {
			$id = $info->id();
			if ( $id === null || ! $id->isAbsolute() ) {
				throw new ParseException( 'Schema id must be a valid URI', $info );
			}
		}

		if ( $hasRef = property_exists( $schema, '$ref' ) ) {
			if ( $this->option( 'allowKeywordsAlongsideRef' ) || $draftObject->allowKeywordsAlongsideRef() ) {
				$hasRef = false;
			}
		}

		$shared = (object) array();

		if ( $this->option( 'allowKeywordValidators' ) ) {
			$keywordValidator = $this->parseKeywordValidators( $info, $draftObject->keywordValidators(), $shared );
		} else {
			$keywordValidator = null;
		}

		return $this->parseSchemaKeywords( $info, $keywordValidator, $draftObject->keywords(), $shared, $hasRef );
	}

	/**
	 * @param SchemaInfo               $info
	 * @param KeywordValidatorParser[] $keywordValidators
	 * @param object                   $shared
	 * @return KeywordValidator|null
	 */
	protected function parseKeywordValidators( $info, $keywordValidators, $shared ) {
		$last = null;

		while ( $keywordValidators ) {
			/** @var KeywordValidatorParser $keywordValidator */
			$keywordValidator = array_pop( $keywordValidators );
			if ( $keywordValidator && ( $keyword = $keywordValidator->parse( $info, $this, $shared ) ) ) {
				$keyword->setNext( $last );
				$last = $keyword;
				unset( $keyword );
			}
			unset( $keywordValidator );
		}

		return $last;
	}

	/**
	 * @param SchemaInfo            $info
	 * @param KeywordValidator|null $keywordValidator
	 * @param KeywordParser[]       $parsers
	 * @param object                $shared
	 * @param bool                  $hasRef
	 * @return Schema
	 */
	protected function parseSchemaKeywords(
		$info,
		$keywordValidator,
		$parsers,
		$shared,
		$hasRef = false
	): Schema {
		/** @var Keyword[] $prepend */
		$prepend = array();
		/** @var Keyword[] $append */
		$append = array();
		/** @var Keyword[] $before */
		$before = array();
		/** @var Keyword[] $after */
		$after = array();
		/** @var Keyword[][] $types */
		$types = array();
		/** @var Keyword[] $ref */
		$ref = array();

		if ( $hasRef ) {
			foreach ( $parsers as $parser ) {
				$kType = $parser->type();

				if ( $kType === KeywordParser::TYPE_APPEND ) {
					$container = &$append;
				} elseif ( $kType === KeywordParser::TYPE_AFTER_REF ) {
					$container = &$ref;
				} elseif ( $kType === KeywordParser::TYPE_PREPEND ) {
					$container = &$prepend;
				} else {
					continue;
				}

				if ( $keyword = $parser->parse( $info, $this, $shared ) ) {
					$container[] = $keyword;
				}

				unset( $container, $keyword, $kType );
			}
		} else {
			foreach ( $parsers as $parser ) {
				$keyword = $parser->parse( $info, $this, $shared );
				if ( $keyword === null ) {
					continue;
				}

				$kType = $parser->type();

				switch ( $kType ) {
					case KeywordParser::TYPE_PREPEND:
						$prepend[] = $keyword;
						break;
					case KeywordParser::TYPE_APPEND:
						$append[] = $keyword;
						break;
					case KeywordParser::TYPE_BEFORE:
						$before[] = $keyword;
						break;
					case KeywordParser::TYPE_AFTER:
						$after[] = $keyword;
						break;
					case KeywordParser::TYPE_AFTER_REF:
						$ref[] = $keyword;
						break;
					default:
						if ( ! isset( $types[ $kType ] ) ) {
							$types[ $kType ] = array();
						}
						$types[ $kType ][] = $keyword;
						break;

				}
			}
		}

		unset( $shared );

		if ( $prepend ) {
			$before = array_merge( $prepend, $before );
		}
		unset( $prepend );

		if ( $ref ) {
			$after = array_merge( $after, $ref );
		}
		unset( $ref );

		if ( $append ) {
			$after = array_merge( $after, $append );
		}
		unset( $append );

		if ( empty( $before ) ) {
			$before = null;
		}
		if ( empty( $after ) ) {
			$after = null;
		}
		if ( empty( $types ) ) {
			$types = null;
		}

		if ( empty( $types ) && empty( $before ) && empty( $after ) ) {
			return new EmptySchema( $info, $keywordValidator );
		}

		return new ObjectSchema( $info, $keywordValidator, $types, $before, $after );
	}
}
