<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:       Black Bar
 * Plugin URI:        https://wordpress.org/plugins/blackbar
 * Description:       Black Bar is a Debug Bar for WordPress developers.
 * Version:           4.1.0
 * Author:            Paul Ryley
 * Author URI:        https://profiles.wordpress.org/pryley#content-plugins
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Text Domain:       blackbar
 * Domain Path:       languages
 */
defined('ABSPATH') || exit;

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/compatibility.php';

if (!defined('SAVEQUERIES')) {
    define('SAVEQUERIES', 1);
}
GeminiLabs\BlackBar\Application::load()->init();
