=== Black Bar ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: blackbar, black bar, debug bar, debugbar, debugging, development, blackbox
Requires at least: 4.0.0
Requires PHP: 5.4.0
Tested up to: 4.9
Stable tag: 1.2.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Black Bar is a Debug Bar for WordPress developers. It collects and displays errors, executed queries, globals, theme templates, and provides a profiler.

== Description ==

Black Bar is an unobtrusive Debug Bar for WordPress developers that attaches itself to the bottom of the browser window. It is a rewrite of [blackbox-debug-bar](https://wordpress.org/plugins/blackbox-debug-bar/) by [Grzegorz Winiarski](https://profiles.wordpress.org/gwin) which appears to be abandoned as it has not been updated since 2013.

How it helps you with development:

- Debug both the front-end and admin area
- Displays any PHP errors that occur when loading a page
- Displays executed MySQL queries and the time it took to execute each query
- Displays the loaded template files of the active theme
- Inspect global variables (COOKIE, GET, POST, SERVER, SESSION)
- Use the Profiler for measuring performance of your plugins and themes

== Installation ==

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Black Bar" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Black Bar plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I use the Profiler? =

To use the profiler, simply put the following line of PHP before and after the code you wish to profile:

`apply_filters( 'debug', 'Enter a description here' );`

== Changelog ==

= 1.2.0 (2018-04-23) =

- Display loaded templates from the active theme
- Fixed CSS styles

= 1.1.0 (2018-04-21) =

- Lowered PHP requirement to 5.4
- Fixed CSS styles
- Fixed plugin activation class
- Fixed query logging

= 1.0.0 (2018-02-22) =

- Initial plugin release
