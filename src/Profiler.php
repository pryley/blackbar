<?php

namespace GeminiLabs\BlackBar;

class Profiler
{
	/**
	 * This is the time that WordPress takes to execute the profiler hook
	 * @var int
	 */
	protected $noise = 0;

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
	 * @param array $timer
	 * @return string
	 */
	public function getTimeString( $timer )
	{
		$timer = $this->normalize( $timer );
		$index = array_search( $timer['name'], array_column( $this->timers, 'name' ));
		$start = $this->start + ( $index * $this->noise );
		$time = number_format( round(( $timer['time'] - $start ) * 1000, 4 ), 4 );
		return sprintf( '%s %s', $time, __( 'ms', 'blackbar' ));
	}

	/**
	 * @return int Microseconds
	 */
	public function getTotalTime()
	{
		$totalNoise = ( count( $this->timers ) - 1 ) * $this->noise;
		return $this->stop - $this->start - $totalNoise;
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
		if( $name === 'blackbar/profiler/noise' ) {
			$this->noise = $microtime - $this->start;
			return;
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
