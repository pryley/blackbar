<?php

namespace GeminiLabs\Blackbar;

use DateTime;
use ReflectionClass;

class Console
{
	public $entries = array();

	/**
	 * @param string $message
	 * @return static
	 */
	public function store( $message, $location = '' )
	{
		$this->entries[] = array(
			'errno' => E_NOTICE,
			'message' => $location.$this->normalizeValue( $message ),
			'name' => 'Debug',
		);
		return $this;
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	protected function isObjectOrArray( $value )
	{
		return is_object( $value ) || is_array( $value );
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	protected function normalizeValue( $value )
	{
		if( $value instanceof DateTime ) {
			$value = $value->format( 'Y-m-d H:i:s' );
		}
		else if( $this->isObjectOrArray( $value )) {
			$value = print_r( $value, true );
		}
		else if( is_resource( $value )) {
			$value = (string)$value;
		}
		return esc_html( $value );
	}
}
