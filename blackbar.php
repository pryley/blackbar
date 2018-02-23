<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Black Bar
 * Plugin URI:  https://wordpress.org/plugins/blackbar
 * Description: Black Bar is a Debug Bar for WordPress developers.
 * Version:     1.0.0
 * Author:      Paul Ryley
 * Author URI:  https://profiles.wordpress.org/pryley#content-plugins
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: blackbar
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

require_once __DIR__.'/activate.php';
require_once __DIR__.'/autoload.php';

if( !GL_BlackBar_Activate::shouldDeactivate() ) {
	(new GeminiLabs\BlackBar\Application)->init();
}
