<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Blackbar
 * Plugin URI:  https://wordpress.org/plugins/blackbar
 * Description: Blackbar is a development tool that displays executed queries, global variables, notices, warnings, theme templates, and a profiler.
 * Version:     1.0.0
 * Author:      Paul Ryley
 * Author URI:  http://geminilabs.io
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: blackbar
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;
require_once __DIR__.'/autoload.php';
(new \GeminiLabs\Blackbar\Application)->init();
