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

namespace Opis\JsonSchema\Errors;

use Opis\JsonSchema\JsonPointer;

class ErrorFormatter {

	/**
	 * @param ValidationError                               $error
	 * @param bool                                          $multiple True if the same key can have multiple errors
	 * @param ?callable(ValidationError,?string=null):mixed $formatter
	 * @param ?callable(ValidationError):string             $key_formatter
	 * @return array
	 */
	public function format(
		$error,
		$multiple = true,
		$formatter = null,
		$key_formatter = null
	): array {
		if ( ! $formatter ) {
			$formatter = array( $this, 'formatErrorMessage' );
		}

		if ( ! $key_formatter ) {
			$key_formatter = array( $this, 'formatErrorKey' );
		}

		$list = array();

		/**
		 * @var ValidationError $error
		 * @var string $message
		 */

		foreach ( $this->getErrors( $error ) as $error => $message ) {
			$key = $key_formatter( $error );

			if ( $multiple ) {
				if ( ! isset( $list[ $key ] ) ) {
					$list[ $key ] = array();
				}
				$list[ $key ][] = $formatter( $error, $message );
			} elseif ( ! isset( $list[ $key ] ) ) {
					$list[ $key ] = $formatter( $error, $message );
			}
		}

		return $list;
	}

	/**
	 * @param ValidationError|null $error
	 * @param string               $mode One of: flag, basic, detailed or verbose
	 * @return array
	 */
	public function formatOutput( $error, $mode = 'flag' ): array {
		if ( $error === null ) {
			return array( 'valid' => true );
		}

		if ( $mode === 'flag' ) {
			return array( 'valid' => false );
		}

		if ( $mode === 'basic' ) {
			return array(
				'valid'  => false,
				'errors' => $this->formatFlat( $error, array( $this, 'formatOutputError' ) ),
			);
		}

		if ( $mode === 'detailed' || $mode === 'verbose' ) {
			$isVerbose = $mode === 'verbose';

			return $this->getNestedErrors(
				$error,
				function ( ValidationError $error, $subErrors = null ) use ( $isVerbose ) {
					$info = $this->formatOutputError( $error );

					$info['valid'] = false;

					if ( $isVerbose ) {
						$id = $error->schema()->info();
						$id = $id->root() ?? $id->id();
						if ( $id ) {
							$id = rtrim( $id, '#' );
						}
						$info['absoluteKeywordLocation'] = $id . $info['keywordLocation'];
					}

					if ( $subErrors ) {
						$info['errors'] = $subErrors;
						if ( ! $isVerbose ) {
							unset( $info['error'] );
						}
					}

					return $info;
				}
			);
		}

		return array( 'valid' => false );
	}

	/**
	 * @param ValidationError                         $error
	 * @param ?callable(ValidationError,?array):mixed $formatter
	 * @return mixed
	 */
	public function formatNested( $error, $formatter = null ) {
		if ( ! $formatter ) {
			$formatter = function ( ValidationError $error, $subErrors = null ): array {
				$ret = array(
					'message' => $this->formatErrorMessage( $error ),
					'keyword' => $error->keyword(),
					'path'    => $this->formatErrorKey( $error ),
				);

				if ( $subErrors ) {
					$ret['errors'] = $subErrors;
				}

				return $ret;
			};
		}

		return $this->getNestedErrors( $error, $formatter );
	}

	/**
	 * @param ValidationError                  $error
	 * @param ?callable(ValidationError):mixed $formatter
	 * @return array
	 */
	public function formatFlat( $error, $formatter = null ): array {
		if ( ! $formatter ) {
			$formatter = array( $this, 'formatErrorMessage' );
		}

		$list = array();

		foreach ( $this->getFlatErrors( $error ) as $error ) {
			$list[] = $formatter( $error );
		}

		return $list;
	}

	/**
	 * @param ValidationError                   $error
	 * @param ?callable(ValidationError):mixed  $formatter
	 * @param ?callable(ValidationError):string $key_formatter
	 * @return array
	 */
	public function formatKeyed(
		$error,
		$formatter = null,
		$key_formatter = null
	): array {
		if ( ! $formatter ) {
			$formatter = array( $this, 'formatErrorMessage' );
		}

		if ( ! $key_formatter ) {
			$key_formatter = array( $this, 'formatErrorKey' );
		}

		$list = array();

		foreach ( $this->getLeafErrors( $error ) as $error ) {
			$key = $key_formatter( $error );

			if ( ! isset( $list[ $key ] ) ) {
				$list[ $key ] = array();
			}

			$list[ $key ][] = $formatter( $error );
		}

		return $list;
	}

	/**
	 * @param ValidationError $error
	 * @param string|null     $message The message to use, if null $error->message() is used
	 * @return string
	 */
	public function formatErrorMessage( $error, $message = null ): string {
		$message = $message ?? $error->message();
		$args    = $this->getDefaultArgs( $error ) + $error->args();

		if ( ! $args ) {
			return $message;
		}

		return preg_replace_callback(
			'~{([^}]+)}~imu',
			static function ( array $m ) use ( $args ) {
				if ( ! isset( $args[ $m[1] ] ) ) {
					return $m[0];
				}

				$value = $args[ $m[1] ];

				if ( is_array( $value ) ) {
					return implode( ', ', $value );
				}

				return (string) $value;
			},
			$message
		);
	}

	/**
	 * @param \Opis\JsonSchema\Errors\ValidationError $error
	 */
	public function formatErrorKey( $error ): string {
		return JsonPointer::pathToString( $error->data()->fullPath() );
	}

	/**
	 * @param \Opis\JsonSchema\Errors\ValidationError $error
	 */
	protected function getDefaultArgs( $error ): array {
		$data = $error->data();
		$info = $error->schema()->info();

		$path   = $info->path();
		$path[] = $error->keyword();

		return array(
			'data:type'      => $data->type(),
			'data:value'     => $data->value(),
			'data:path'      => JsonPointer::pathToString( $data->fullPath() ),

			'schema:id'      => $info->id(),
			'schema:root'    => $info->root(),
			'schema:base'    => $info->base(),
			'schema:draft'   => $info->draft(),
			'schema:keyword' => $error->keyword(),
			'schema:path'    => JsonPointer::pathToString( $path ),
		);
	}

	/**
	 * @param \Opis\JsonSchema\Errors\ValidationError $error
	 */
	protected function formatOutputError( $error ): array {
		$path = $error->schema()->info()->path();

		$path[] = $error->keyword();

		return array(
			'keywordLocation'  => JsonPointer::pathToFragment( $path ),
			'instanceLocation' => JsonPointer::pathToFragment( $error->data()->fullPath() ),
			'error'            => $this->formatErrorMessage( $error ),
		);
	}

	/**
	 * @param ValidationError                        $error
	 * @param callable(ValidationError,?array):mixed $formatter
	 * @return mixed
	 */
	protected function getNestedErrors( $error, $formatter ) {
		if ( $subErrors = $error->subErrors() ) {
			foreach ( $subErrors as &$subError ) {
				$subError = $this->getNestedErrors( $subError, $formatter );
				unset( $subError );
			}
		}

		return $formatter( $error, $subErrors );
	}

	/**
	 * @param ValidationError $error
	 * @return iterable|ValidationError[]
	 */
	protected function getFlatErrors( $error ) {
		yield $error;

		foreach ( $error->subErrors() as $subError ) {
			yield from $this->getFlatErrors( $subError );
		}
	}

	/**
	 * @param ValidationError $error
	 * @return iterable|ValidationError[]
	 */
	protected function getLeafErrors( $error ) {
		if ( $subErrors = $error->subErrors() ) {
			foreach ( $subErrors as $subError ) {
				yield from $this->getLeafErrors( $subError );
			}
		} else {
			yield $error;
		}
	}

	/**
	 * @param ValidationError $error
	 * @return iterable
	 */
	protected function getErrors( $error ) {
		$data = $error->schema()->info()->data();

		$map  = null;
		$pMap = null;

		if ( is_object( $data ) ) {
			switch ( $error->keyword() ) {
				case 'required':
					if ( isset( $data->{'$error'}->required ) && is_object( $data->{'$error'}->required ) ) {
						$e     = $data->{'$error'}->required;
						$found = false;
						foreach ( $error->args()['missing'] as $prop ) {
							if ( isset( $e->{$prop} ) ) {
								yield $error => $e->{$prop};
								$found = true;
							}
						}
						if ( $found ) {
							return;
						}
						if ( isset( $e->{'*'} ) ) {
							yield $error => $e->{'*'};
							return;
						}
						unset( $e, $found, $prop );
					}
					break;
				case '$filters':
					if ( ( $args = $error->args() ) && isset( $args['args']['$error'] ) ) {
						yield $error => $args['args']['$error'];
						return;
					}
					unset( $args );
					break;
			}

			if ( isset( $data->{'$error'} ) ) {
				$map = $data->{'$error'};

				if ( is_string( $map ) ) {
					// We have an global error
					yield $error => $map;
					return;
				}

				if ( is_object( $map ) ) {
					if ( isset( $map->{$error->keyword()} ) ) {
						$pMap = $map->{'*'} ?? null;
						$map  = $map->{$error->keyword()};
						if ( is_string( $map ) ) {
							yield $error => $map;
							return;
						}
					} elseif ( isset( $map->{'*'} ) ) {
						yield $error => $map->{'*'};
						return;
					}
				}
			}
		}

		if ( ! is_object( $map ) ) {
			$map = null;
		}

		$subErrors = $error->subErrors();

		if ( ! $subErrors ) {
			yield $error => $pMap ?? $error->message();
			return;
		}

		if ( ! $map ) {
			foreach ( $subErrors as $subError ) {
				yield from $this->getErrors( $subError );
			}
			return;
		}

		foreach ( $subErrors as $subError ) {
			$path = $subError->data()->path();
			if ( count( $path ) !== 1 ) {
				yield from $this->getErrors( $subError );
			} else {
				$path = $path[0];
				if ( isset( $map->{$path} ) ) {
					yield $subError => $map->{$path};
				} elseif ( isset( $map->{'*'} ) ) {
					yield $subError => $map->{'*'};
				} else {
					yield from $this->getErrors( $subError );
				}
			}
		}
	}
}
