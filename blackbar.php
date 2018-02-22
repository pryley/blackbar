<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: BlackBar
 * Plugin URI:  https://wordpress.org/plugins/blackbar
 * Description: BlackBar is a development tool that displays executed queries, global variables, notices, warnings, theme templates, and a profiler.
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
if( \GL_BlackBar_Activate::shouldDeactivate() )return;

require_once __DIR__.'/autoload.php';
(new \GeminiLabs\Blackbar\Application)->init();
