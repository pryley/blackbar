<?php

namespace GeminiLabs\Blackbar;

use DateTime;
use ReflectionClass;

class Console
{
	const ERROR_CODES = array(
		E_ERROR => 'Error', // 1
		E_WARNING => 'Warning', // 2
		E_NOTICE => 'Notice', // 8
		E_STRICT => 'Strict', // 2048
		E_DEPRECATED => 'Deprecated', // 8192
	);

	const MAPPED_ERROR_CODES = array(
		'debug' => 0,
		'info' => 0,
		'notice' => 0,
		'warning' => E_NOTICE, // 8
		'error' => E_WARNING, // 2
		'critical' => E_WARNING, // 2
		'alert' => E_WARNING, // 2
		'emergency' => E_WARNING, // 2
	);

	public $entries = array();

	/**
	 * @param string $message
	 * @return static
	 */
	public function store( $message, $errno = 0, $location = '' )
	{
		$errname = 'Debug';
		if( array_key_exists( $errno, static::MAPPED_ERROR_CODES )) {
			$errname = ucfirst( $errno );
			$errno = static::MAPPED_ERROR_CODES[$errno];
		}
		else if( array_key_exists( $errno, static::ERROR_CODES )) {
			$errname = static::ERROR_CODES[$errno];
		}
		$this->entries[] = array(
			'errno' => $errno,
			'message' => $location.$this->normalizeValue( $message ),
			'name' => sprintf( '<span class="glbb-info glbb-%s">%s</span>', strtolower( $errname ), $errname ),
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
			$value = print_r( json_decode( json_encode( $value )), true );
		}
		else if( is_resource( $value )) {
			$value = (string)$value;
		}
		return esc_html( $value );
	}
}
