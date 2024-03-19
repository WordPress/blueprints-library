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

use Opis\JsonSchema\ContentEncoding;

class ContentEncodingResolver {

	/** @var callable[]|ContentEncoding[] */
	protected $list;

	/** @var callable|ContentEncoding|null */
	protected $defaultEncoding = null;

	/**
	 * @param callable[]|ContentEncoding[]  $list
	 * @param callable|ContentEncoding|null $defaultEncoding
	 */
	public function __construct( array $list = array(), $defaultEncoding = null ) {
		$list += array(
			'binary'           => self::class . '::DecodeBinary',
			'base64'           => self::class . '::DecodeBase64',
			'quoted-printable' => self::class . '::DecodeQuotedPrintable',
		);

		$this->list            = $list;
		$this->defaultEncoding = $defaultEncoding;
	}

	/**
	 * @param string $name
	 * @return callable|ContentEncoding|string|null
	 */
	public function resolve( $name ) {
		return $this->list[ $name ] ?? $this->defaultEncoding;
	}

	/**
	 * @param string          $name
	 * @param ContentEncoding $encoding
	 * @return ContentEncodingResolver
	 */
	public function register( $name, $encoding ): self {
		$this->list[ $name ] = $encoding;

		return $this;
	}

	/**
	 * @param string   $name
	 * @param callable $encoding
	 * @return ContentEncodingResolver
	 */
	public function registerCallable( $name, $encoding ): self {
		$this->list[ $name ] = $encoding;

		return $this;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function unregister( $name ): bool {
		if ( isset( $this->list[ $name ] ) ) {
			unset( $this->list[ $name ] );

			return true;
		}

		return false;
	}

	/**
	 * @param callable|ContentEncoding|null $handler
	 * @return $this
	 */
	public function setDefaultHandler( $handler ): self {
		$this->defaultEncoding = $handler;
		return $this;
	}

	public function __serialize(): array {
		return array(
			'list'            => $this->list,
			'defaultEncoding' => $this->defaultEncoding,
		);
	}

	public function __unserialize( array $data ) {
		$this->list            = $data['list'];
		$this->defaultEncoding = $data['defaultEncoding'] ?? null;
	}

	/**
	 * @param string $value
	 */
	public static function DecodeBinary( $value ) {
		return $value;
	}

	/**
	 * @param string $value
	 */
	public static function DecodeBase64( $value ) {
		$value = base64_decode( $value, true );

		return is_string( $value ) ? $value : null;
	}

	/**
	 * @param string $value
	 */
	public static function DecodeQuotedPrintable( $value ) {
		return quoted_printable_decode( $value );
	}
}
