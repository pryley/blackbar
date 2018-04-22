<?php

defined( 'WPINC' ) || die;

/**
 * PSR-4 autoloader
 */
spl_autoload_register( function( $class )
{
	$namespaces = [
		'GeminiLabs\\BlackBar\\' => __DIR__.'/src/',
		'GeminiLabs\\BlackBar\\Tests\\' => __DIR__.'/tests/',
	];
	foreach( $namespaces as $prefix => $base_dir ) {
		$len = strlen( $prefix );
		if( strncmp( $prefix, $class, $len ) !== 0 )continue;
		$file = $base_dir.str_replace( '\\', '/', substr( $class, $len )).'.php';
		if( !file_exists( $file ))continue;
		require $file;
		break;
	}
});
