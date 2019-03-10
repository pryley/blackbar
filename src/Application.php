<?php

namespace GeminiLabs\BlackBar;

use GeminiLabs\BlackBar\Controller;
use GeminiLabs\BlackBar\Console;
use GeminiLabs\BlackBar\Profiler;

final class Application
{
	const CONSOLE_HOOK = 'console';
	const ID = 'blackbar';
	const LANG = '/languages/';
	const PROFILER_HOOK = 'profile';

	public $console;
	public $errors = array();
	public $file;
	public $profiler;

	public function __construct()
	{
		$this->console = new Console;
		$this->file = realpath( dirname( __DIR__ ).'/'.static::ID.'.php' );
		$this->profiler = new Profiler;
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
		$errname = array_key_exists( $errno, Console::ERROR_CODES )
			? Console::ERROR_CODES[$errno]
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
	 * @return void
	 */
	public function init()
	{
		$controller = new Controller( $this );
		add_filter( 'all',            array( $controller, 'initConsole' ));
		add_filter( 'all',            array( $controller, 'initProfiler' ));
		add_action( 'plugins_loaded', array( $controller, 'registerLanguages' ));
		add_action( 'init', function() use( $controller ) {
			if( !apply_filters( 'blackbar/enabled', true ))return;
			add_action( 'admin_enqueue_scripts', array( $controller, 'enqueueAssets' ));
			add_action( 'wp_enqueue_scripts',    array( $controller, 'enqueueAssets' ));
			add_action( 'admin_footer',          array( $controller, 'renderBar' ));
			add_action( 'wp_footer',             array( $controller, 'renderBar' ));
			add_filter( 'admin_body_class',      array( $controller, 'filterBodyClasses' ));
		});
		apply_filters( 'debug', 'Profiler Started' );
		apply_filters( 'debug', 'blackbar/profiler/noise' );
		set_error_handler( array( $this, 'errorHandler' ), E_ALL|E_STRICT );
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function path( $file = '' )
	{
		return plugin_dir_path( $this->file ).ltrim( trim( $file ), '/' );
	}

	/**
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
	 * @param string $path
	 * @return string
	 */
	public function url( $path = '' )
	{
		return esc_url( plugin_dir_url( $this->file ).ltrim( trim( $path ), '/' ));
	}
}
