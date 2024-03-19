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

namespace Opis\JsonSchema\Resolvers;

use Opis\JsonSchema\{Helper, Filter};
use Opis\JsonSchema\Filters\{CommonFilters,
	DataExistsFilter,
	DateTimeFilters,
	FilterExistsFilter,
	FormatExistsFilter,
	SchemaExistsFilter,
	SlotExistsFilter,
	GlobalVarExistsFilter};

class FilterResolver {

	/** @var Filter[][][] */
	protected $filters = array();

	/** @var FilterResolver[] */
	protected $ns = array();

	/**
	 * @var string
	 */
	protected $separator;

	/**
	 * @var string
	 */
	protected $defaultNS;

	/**
	 * FilterResolver constructor.
	 *
	 * @param string $ns_separator
	 * @param string $default_ns
	 */
	public function __construct( string $ns_separator = '::', string $default_ns = 'default' ) {
		$this->separator = $ns_separator;
		$this->defaultNS = $default_ns;

		$this->registerDefaultFilters();
	}

	/**
	 * You can override this to add/remove default filters
	 */
	protected function registerDefaultFilters() {
		$this->registerMultipleTypes( 'schema-exists', new SchemaExistsFilter() );
		$this->registerMultipleTypes( 'data-exists', new DataExistsFilter() );
		$this->registerMultipleTypes( 'global-exists', new GlobalVarExistsFilter() );
		$this->registerMultipleTypes( 'slot-exists', new SlotExistsFilter() );
		$this->registerMultipleTypes( 'filter-exists', new FilterExistsFilter() );
		$this->registerMultipleTypes( 'format-exists', new FormatExistsFilter() );

		$cls = DateTimeFilters::class . '::';
		$this->registerCallable( 'string', 'min-date', $cls . 'MinDate' );
		$this->registerCallable( 'string', 'max-date', $cls . 'MaxDate' );
		$this->registerCallable( 'string', 'not-date', $cls . 'NotDate' );
		$this->registerCallable( 'string', 'min-time', $cls . 'MinTime' );
		$this->registerCallable( 'string', 'max-time', $cls . 'MaxTime' );
		$this->registerCallable( 'string', 'min-datetime', $cls . 'MinDateTime' );
		$this->registerCallable( 'string', 'max-datetime', $cls . 'MaxDateTime' );

		$cls = CommonFilters::class . '::';
		$this->registerCallable( 'string', 'regex', $cls . 'Regex' );
		$this->registerMultipleTypes( 'equals', $cls . 'Equals' );
	}


	/**
	 * @param string $name
	 * @param string $type
	 * @return Filter|callable|null
	 */
	public function resolve( $name, $type ) {
		list($ns, $name) = $this->parseName( $name );

		if ( isset( $this->filters[ $ns ][ $name ] ) ) {
			return $this->filters[ $ns ][ $name ][ $type ] ?? null;
		}

		if ( ! isset( $this->ns[ $ns ] ) ) {
			return null;
		}

		$this->filters[ $ns ][ $name ] = $this->ns[ $ns ]->resolveAll( $name );

		return $this->filters[ $ns ][ $name ][ $type ] ?? null;
	}

	/**
	 * @param string $name
	 * @return Filter[]|callable[]|null
	 */
	public function resolveAll( $name ) {
		list($ns, $name) = $this->parseName( $name );

		if ( isset( $this->filters[ $ns ][ $name ] ) ) {
			return $this->filters[ $ns ][ $name ];
		}

		if ( ! isset( $this->ns[ $ns ] ) ) {
			return null;
		}

		return $this->filters[ $ns ][ $name ] = $this->ns[ $ns ]->resolveAll( $name );
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param Filter $filter
	 * @return FilterResolver
	 */
	public function register( $type, $name, $filter ): self {
		list($ns, $name) = $this->parseName( $name );

		$this->filters[ $ns ][ $name ][ $type ] = $filter;

		return $this;
	}

	/**
	 * @param string      $name
	 * @param string|null $type
	 * @return bool
	 */
	public function unregister( $name, $type = null ): bool {
		list($ns, $name) = $this->parseName( $name );
		if ( ! isset( $this->filters[ $ns ][ $name ] ) ) {
			return false;
		}

		if ( $type === null ) {
			unset( $this->filters[ $ns ][ $name ] );

			return true;
		}

		if ( isset( $this->filters[ $ns ][ $name ][ $type ] ) ) {
			unset( $this->filters[ $ns ][ $name ][ $type ] );

			return true;
		}

		return false;
	}

	/**
	 * @param string          $name
	 * @param callable|Filter $filter
	 * @param array|null      $types
	 * @return FilterResolver
	 */
	public function registerMultipleTypes( $name, $filter, $types = null ): self {
		list($ns, $name) = $this->parseName( $name );

		$types = $types ?? Helper::JSON_TYPES;

		foreach ( $types as $type ) {
			$this->filters[ $ns ][ $name ][ $type ] = $filter;
		}

		return $this;
	}

	/**
	 * @param string   $type
	 * @param string   $name
	 * @param callable $filter
	 * @return FilterResolver
	 */
	public function registerCallable( $type, $name, $filter ): self {
		list($ns, $name) = $this->parseName( $name );

		$this->filters[ $ns ][ $name ][ $type ] = $filter;

		return $this;
	}

	/**
	 * @param string         $ns
	 * @param FilterResolver $resolver
	 * @return FilterResolver
	 */
	public function registerNS( $ns, $resolver ): self {
		$this->ns[ $ns ] = $resolver;

		return $this;
	}

	/**
	 * @param string $ns
	 * @return bool
	 */
	public function unregisterNS( $ns ): bool {
		if ( isset( $this->filters[ $ns ] ) ) {
			unset( $this->filters[ $ns ] );
			unset( $this->ns[ $ns ] );

			return true;
		}

		if ( isset( $this->ns[ $ns ] ) ) {
			unset( $this->ns[ $ns ] );

			return true;
		}

		return false;
	}

	public function __serialize(): array {
		return array(
			'separator' => $this->separator,
			'defaultNS' => $this->defaultNS,
			'ns'        => $this->ns,
			'filters'   => $this->filters,
		);
	}

	public function __unserialize( array $data ) {
		$this->separator = $data['separator'];
		$this->defaultNS = $data['defaultNS'];
		$this->ns        = $data['ns'];
		$this->filters   = $data['filters'];
	}

	/**
	 * @param string $name
	 * @return array
	 */
	protected function parseName( $name ): array {
		$name = strtolower( $name );

		if ( strpos( $name, $this->separator ) === false ) {
			return array( $this->defaultNS, $name );
		}

		return explode( $this->separator, $name, 2 );
	}
}
