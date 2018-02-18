<?php

namespace GeminiLabs\Blackbar;

class Profiler
{
	/**
	 * @var array
	 */
	protected $timers = array();

	/**
	 * @var int
	 */
	protected $start = null;

	/**
	 * @var int
	 */
	protected $stop = null;

	/**
	 * @return array
	 */
	public function getMeasure()
	{
		return $this->timers;
	}

	/**
	 * @param array $timer
	 * @return string
	 */
	public function getMemoryString( $timer )
	{
		$timer = $this->normalize( $timer );
		return sprintf( '%s %s', round( $timer['memory'] / 1000 ), __( 'kB', 'blackbar' ));
	}

	/**
	 * @param array $timer
	 * @return string
	 */
	public function getNameString( $timer )
	{
		$timer = $this->normalize( $timer );
		return $timer['name'];
	}

	/**
	 * @return int
	 */
	public function getStartTime()
	{
		return $this->start;
	}

	/**
	 * @param array $timer
	 * @return string
	 */
	public function getTimeString( $timer )
	{
		$timer = $this->normalize( $timer );
		$time = number_format( round(( $timer['time'] - $this->start ) * 1000, 4 ), 4 );
		return sprintf( '%s %s', $time, __( 'ms', 'blackbar' ));
	}

	/**
	 * @return int Microseconds
	 */
	public function getTotalTime()
	{
		return $this->stop - $this->start;
	}

	/**
	 * @param string $name
	 */
	public function trace( $name )
	{
		$microtime = microtime( true );
		if( !$this->start ) {
			$this->start = $microtime;
		}
		$this->timers[] = array(
			'name' => $name,
			'time' => $microtime,
			'memory' => memory_get_peak_usage(),
		);
		$this->stop = $microtime;
	}

	/**
	 * @return array
	 */
	protected function normalize( $timer )
	{
		return wp_parse_args( (array) $timer, array(
			'name' => '',
			'time' => 0,
			'memory' => 0,
		));
	}
}
