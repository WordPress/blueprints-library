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

namespace Opis\JsonSchema\Filters;

use Opis\JsonSchema\{ValidationContext, Filter, Schema};

class GlobalVarExistsFilter implements Filter {

	/**
	 * @inheritDoc
	 * @param \Opis\JsonSchema\ValidationContext $context
	 * @param \Opis\JsonSchema\Schema            $schema
	 * @param mixed[]                            $args
	 */
	public function validate( $context, $schema, $args = array() ): bool {
		$var = $args['var'] ?? $context->currentData();

		if ( ! is_string( $var ) ) {
			return false;
		}

		$globals = $context->globals();

		if ( ! array_key_exists( $var, $globals ) ) {
			return false;
		}

		if ( array_key_exists( 'value', $args ) ) {
			return $globals[ $var ] == $args['value'];
		}

		return true;
	}
}
