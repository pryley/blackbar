<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Black Bar
 * Plugin URI:  https://wordpress.org/plugins/blackbar
 * Description: Black Bar is a Debug Bar for WordPress developers.
 * Version:     2.2.1
 * Author:      Paul Ryley
 * Author URI:  https://profiles.wordpress.org/pryley#content-plugins
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: blackbar
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

if( !class_exists( 'GL_Plugin_Check_v3' )) {
	require_once __DIR__.'/activate.php';
}
if( !(new GL_Plugin_Check_v3( __FILE__, array( 'php' => '5.6', 'wordpress' => '4.7.0' )))->canProceed() )return;
require_once __DIR__.'/autoload.php';
require_once __DIR__.'/compatibility.php';

if( !defined( 'SAVEQUERIES' )) {
	define( 'SAVEQUERIES', 1 );
}
(new GeminiLabs\BlackBar\Application)->init();
