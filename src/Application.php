<?php

namespace GeminiLabs\Blackbar;

use GeminiLabs\Blackbar\Profiler;

final class Application
{
	const DEBUG = 'debug';
	const ID = 'blackbar';
	const LANG = '/languages/';

	protected $errors = array();
	protected $file;
	protected $profiler;

	public function __construct()
	{
		$this->file = realpath( dirname( __DIR__ ).'/'.static::ID.'.php' );
		$this->profiler = new Profiler;
	}

	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		wp_enqueue_script( static::ID, $this->url( 'assets/main.js' ));
		wp_enqueue_style( static::ID, $this->url( 'assets/main.css' ), array( 'dashicons' ));
		wp_enqueue_style( static::ID.'-syntax', $this->url( 'assets/syntax.css' ));
	}

	/**
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @return void
	 */
	public function errorHandler( $errno, $errstr, $errfile, $errline )
	{
		$errorCodes = array(
			2 => 'Warning',
			8 => 'Notice',
			2048 => 'Strict',
			8192 => 'Deprecated',
		);
		$errname = array_key_exists( $errno, $errorCodes )
			? $errorCodes[$errno]
			: 'Unknown';
		$hash = md5( $errno.$errstr.$errfile.$errline );
		if( array_key_exists( $hash, $this->errors )) {
			$this->errors[$hash]['count']++;
		}
		else {
			$this->errors[$hash] = array(
				"errno" => $errno,
				"message" => $errstr,
				"file" => $errfile,
				"name" => $errname,
				"line" => $errline,
				"count" => 0,
			);
		}
	}

	/**
	 * @param string $classes
	 * @return string
	 */
	public function filterBodyClasses( $classes )
	{
		return trim( $classes.' '.static::ID );
	}

	/**
	 * @return array
	 */
	public function filterNoconflictScripts( $scripts )
	{
		$scripts[] = static::ID;
		return $scripts;
	}

	/**
	 * @return array
	 */
	public function filterNoconflictStyles( $styles )
	{
		$styles[] = static::ID;
		$styles[] = static::ID.'-syntax';
		return $styles;
	}

	/**
	 * @return void
	 */
	public function init()
	{
		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueueAssets' ));
		add_action( 'admin_footer',             array( $this, 'renderBar' ));
		add_action( 'plugins_loaded',           array( $this, 'registerLanguages' ));
		add_action( 'wp_enqueue_scripts',       array( $this, 'enqueueAssets' ));
		add_action( 'wp_footer',                array( $this, 'renderBar' ));
		add_filter( 'admin_body_class',         array( $this, 'filterBodyClasses' ));
		add_filter( 'all',                      array( $this, 'initProfiler' ));
		add_filter( 'gform_noconflict_scripts', array( $this, 'filterNoconflictScripts' ) );
		add_filter( 'gform_noconflict_styles',  array( $this, 'filterNoconflictStyles' ) );
		apply_filters( 'debug', 'Profiler Initiated' );
		apply_filters( 'debug', 'Profiler Noise' );
		set_error_handler( array( $this, 'errorHandler' ), E_ALL|E_STRICT );
	}

	/**
	 * @return void
	 * @action all
	 */
	public function initProfiler()
	{
		if( func_get_arg(0) != static::DEBUG )return;
		$this->profiler->trace( func_get_arg(1) );
	}

	/**
	 * @return void
	 */
	public function registerLanguages()
	{
		load_plugin_textdomain( static::ID, false, plugin_basename( $this->path() ).static::LANG );
	}

	/**
	 * Render a view and pass any provided data to the view
	 *
	 * @param string $view
	 * @return void
	 */
	public function render( $view, array $data = array() )
	{
		$file = $this->path( sprintf( 'views/%s.php', str_replace( '.php', '', $view )));
		if( !file_exists( $file ))return;
		extract( $data );
		include $file;
	}

	/**
	 * @return void
	 * @action admin_footer
	 * @action wp_footer
	 */
	public function renderBar()
	{
		apply_filters( 'debug', 'Profiler Stopped' );
		$this->render( 'debug-bar', array(
			'blackbar' => $this,
			'errors' => $this->getErrors(),
			'errorsLabel' => $this->getErrorsLabel(),
			'profiler' => $this->profiler,
			'profilerLabel' => $this->getProfilerLabel(),
			'queries' => $this->getQueries(),
			'queriesLabel' => $this->getQueriesLabel(),
		));
	}

	/**
	 * @return string
	 */
	protected function convertToMiliseconds( $time, $decimals = 2 )
	{
		return number_format( $time * 1000, $decimals );
	}

	/**
	 * @return array
	 */
	protected function getErrors()
	{
		$errors = array();
		foreach( $this->errors as $error ) {
			if( $error['errno'] == E_WARNING ) {
				$error['name'] = '<span class="glbb-error">'.$error['name'].'</span>';
			}
			if( $error['count'] > 1 ) {
				$error['name'] .= ' ('.$error['count'].')';
			}
			$errors[] = array(
				'name' => $error['name'],
				'message' => sprintf( __( '%s on line %s in file %s', 'blackbar' ),
					$error['message'],
					$error['line'],
					$error['file']
				),
			);
		}
		return $errors;
	}

	/**
	 * @return string
	 */
	protected function getErrorsLabel()
	{
		$warningCount = 0;
		foreach( $this->errors as $error ) {
			if( $error['errno'] == E_WARNING ) {
				$warningCount++;
			}
		}
		$errorCount = count( $this->errors );
		$warnings = $warningCount > 0 ? sprintf( ', %d!', $warningCount ) : '';
		return sprintf( __( 'Errors (%d%s)', 'blackbar' ), $errorCount, $warnings );
	}

	/**
	 * @return string
	 */
	protected function getProfilerLabel()
	{
		$profilerTime = $this->convertToMiliseconds( $this->profiler->getTotalTime(), 0 );
		return sprintf( __( 'Profiler (%s ms)', 'blackbar' ), $profilerTime );
	}

	/**
	 * @return array
	 */
	protected function getQueries()
	{
		global $wpdb;
		$queries = array();
		$search = array(
			'AND',
			'FROM',
			'GROUP BY',
			'INNER JOIN',
			'LIMIT',
			'ON DUPLICATE KEY UPDATE',
			'ORDER BY',
			'SET',
			'WHERE',
		);
		$replace = array_map( function( $value ) {
			return PHP_EOL.$value;
		}, $search );
		foreach( $wpdb->queries as $query ) {
			$miliseconds = number_format( round( $query[1] * 1000, 4 ), 4 );
			$sql = preg_replace( '/\s\s+/', ' ', trim( $query[0] ));
			$sql = str_replace( PHP_EOL, ' ', $sql );
			$sql = str_replace( $search, $replace, $sql );
			$queries[] = array(
				'ms' => $miliseconds,
				'sql' => $sql,
			);
		}
		return $queries;
	}

	/**
	 * @return string
	 */
	protected function getQueriesLabel()
	{
		global $wpdb;
		$queryTime = 0;
		foreach( $wpdb->queries as $query ) {
			$queryTime += $query[1];
		}
		$queriesCount = '<span class="glbb-queries-count">'.count( $wpdb->queries ).'</span>';
		$queriesTime = '<span class="glbb-queries-time">'.$this->convertToMiliseconds( $queryTime ).'</span>';
		return sprintf( __( 'SQL (%s queries in %s ms)', 'blackbar' ), $queriesCount, $queriesTime );
	}

	/**
	 * @param string $file
	 * @return string
	 */
	protected function path( $file = '' )
	{
		return plugin_dir_path( $this->file ).ltrim( trim( $file ), '/' );
	}

	/**
	 * @param string $path
	 * @return string
	 */
	protected function url( $path = '' )
	{
		return esc_url( plugin_dir_url( $this->file ) . ltrim( trim( $path ), '/' ));
	}
}
