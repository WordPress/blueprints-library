<?php

namespace WordPress\JsonMapper;

class Utils {

	static public $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	/**
  * @param string $type
  */
 static public function is_type_scalar( $type ) {
		return in_array( $type, self::$scalar_types, true );
	}

}
