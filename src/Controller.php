<?php

namespace GeminiLabs\BlackBar;

use GeminiLabs\BlackBar\Application;

class Controller
{
	/**
	 * @var Application
	 */
	protected $app;

	public function __construct( Application $app )
	{
		$this->app = $app;
	}

	/**
	 * @return void
	 * @action admin_enqueue_scripts
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		wp_enqueue_script( Application::ID, $this->app->url( 'assets/main.js' ));
		wp_enqueue_style( Application::ID, $this->app->url( 'assets/main.css' ), array( 'dashicons' ));
		wp_enqueue_style( Application::ID.'-syntax', $this->app->url( 'assets/syntax.css' ));
	}

	/**
	 * @param string $classes
	 * @return string
	 * @action admin_body_class
	 */
	public function filterBodyClasses( $classes )
	{
		return trim( $classes.' '.Application::ID );
	}

	/**
	 * @return void
	 * @filter all
	 */
	public function initConsole()
	{
		if( func_get_arg(0) != Application::CONSOLE_HOOK )return;
		$args = array_pad( func_get_args(), 4, '' );
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 4 );
		$entry = array_pop( $backtrace );
		$location = $args[3];
		if( empty( trim( $location )) && array_key_exists( 'file', $entry )) {
			$path = explode( ABSPATH, $entry['file'] );
			$location = sprintf( '%s:%s', array_pop( $path ), $entry['line'] );
		}
		$this->app->console->store( $args[1], $args[2], '['.$location.'] ' );
	}

	/**
	 * @return void
	 * @filter all
	 */
	public function initProfiler()
	{
		if( func_get_arg(0) != Application::PROFILER_HOOK )return;
		$this->app->profiler->trace( func_get_arg(1) );
	}

	/**
	 * @return void
	 * @action plugins_loaded
	 */
	public function registerLanguages()
	{
		load_plugin_textdomain( Application::ID, false,
			plugin_basename( $this->app->path() ).Application::LANG
		);
	}

	/**
	 * @return void
	 * @action admin_footer
	 * @action wp_footer
	 */
	public function renderBar()
	{
		apply_filters( 'debug', 'Profiler Stopped' );
		$this->app->render( 'debug-bar', array(
			'blackbar' => $this->app,
			'consoleEntries' => $this->getConsoleEntries(),
			'consoleLabel' => $this->getConsoleLabel(),
			'profiler' => $this->app->profiler,
			'profilerLabel' => $this->getProfilerLabel(),
			'queries' => $this->getQueries(),
			'queriesLabel' => $this->getQueriesLabel(),
			'templates' => $this->getTemplates(),
		));
	}

	/**
	 * @param int $time
	 * @param int $decimals
	 * @return string
	 */
	protected function convertToMiliseconds( $time, $decimals = 2 )
	{
		return number_format( $time * 1000, $decimals );
	}

	/**
	 * @return array
	 */
	protected function getConsoleEntries()
	{
		return array_merge( $this->getErrors(), $this->app->console->entries );
	}

	/**
	 * @return string
	 */
	protected function getConsoleLabel()
	{
		$class = '';
		$entries = $this->getConsoleEntries();
		$errorCount = 0;
		foreach( $entries as $entry ) {
			if( in_array( $entry['errno'], [E_NOTICE, E_STRICT, E_DEPRECATED] )) {
				$class = 'glbb-warning';
			}
			if( in_array( $entry['errno'], [E_WARNING] )) {
				$errorCount++;
			}
		}
		$entryCount = count( $entries );
		if( $errorCount > 0 ) {
			$class = 'glbb-error';
			$entryCount .= sprintf( ', %d!', $errorCount );
		}
		$label = sprintf( __( 'Console (%s)', 'blackbar' ), $entryCount );
		return '<span class="'.$class.'">'.$label.'</span>';
	}

	/**
	 * @return array
	 */
	protected function getErrors()
	{
		$errors = array();
		foreach( $this->app->errors as $error ) {
			$class = 'glbb-info';
			if( in_array( $error['errno'], [E_NOTICE, E_STRICT, E_DEPRECATED] )) {
				$class = 'glbb-warning';
			}
			if( $error['errno'] == E_WARNING ) {
				$class = 'glbb-error';
			}
			if( $error['count'] > 1 ) {
				$error['name'] .= ' ('.$error['count'].')';
			}
			$errors[] = array(
				'errno' => $error['errno'],
				'name' => '<span class="'.$class.'">'.$error['name'].'</span>',
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
	 * @return array
	 */
	protected function getIncludedFiles()
	{
		$files = array_values( array_filter( get_included_files(), function( $file ) {
			$bool = strpos( $file, '/themes/' ) !== false
				&& strpos( $file, '/functions.php' ) === false;
			return (bool)apply_filters( 'blackbar/templates/file', $bool, $file );
		}));
		return array_map( function( $key, $value ) {
			$value = str_replace( trailingslashit( WP_CONTENT_DIR ), '', $value );
			return sprintf( '[%s] => %s', $key, $value );
		}, array_keys( $files ), $files );
	}

	/**
	 * @return string
	 */
	protected function getProfilerLabel()
	{
		$profilerTime = $this->convertToMiliseconds( $this->app->profiler->getTotalTime(), 0 );
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
			'AND', 'FROM', 'GROUP BY', 'INNER JOIN', 'LIMIT', 'ON DUPLICATE KEY UPDATE',
			'ORDER BY', 'SET', 'WHERE',
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
		if( !SAVEQUERIES ) {
			return __( 'SQL', 'blackbar' );
		}
		global $wpdb;
		$queryTime = 0;
		foreach( $wpdb->queries as $query ) {
			$queryTime += $query[1];
		}
		$queriesCount = '<span class="glbb-queries-count">'.count( $wpdb->queries ).'</span>';
		$queriesTime = '<span class="glbb-queries-time">'.$this->convertToMiliseconds( $queryTime ).'</span>';
		return sprintf( __( 'SQL (%s queries | %s ms)', 'blackbar' ), $queriesCount, $queriesTime );
	}

	/**
	 * @return void|string
	 */
	protected function getTemplates()
	{
		if( is_admin() )return;
		if( class_exists( '\GeminiLabs\Castor\Facades\Development' )) {
			ob_start();
			\GeminiLabs\Castor\Facades\Development::printTemplatePaths();
			return ob_get_clean();
		}
		return '<pre>'.implode( PHP_EOL, $this->getIncludedFiles() ).'</pre>';
	}
}
