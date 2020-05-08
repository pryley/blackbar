=== Black Bar ===
Contributors: pryley, geminilabs
Donate link: https://www.paypal.me/pryley
Tags: blackbar, black bar, debug bar, debugbar, debugging, development, blackbox
Requires at least: 4.7.0
Requires PHP: 5.6.0
Tested up to: 5.4
Stable tag: 2.2.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Black Bar is a Debug Bar for WordPress developers. It collects and displays errors, executed queries, globals, theme templates, and provides a profiler.

== Description ==

Black Bar is an unobtrusive Debug Bar for WordPress developers that attaches itself to the bottom of the browser window. It is a rewrite of [blackbox-debug-bar](https://wordpress.org/plugins/blackbox-debug-bar/) by [Grzegorz Winiarski](https://profiles.wordpress.org/gwin) which appears to be abandoned as it has not been updated since 2013.

How it helps you with development:

- Displays any PHP errors that occur when loading a page
- Displays executed MySQL queries and the time it took to execute each query
- Displays the loaded template files of the active theme
- Inspect global variables (COOKIE, GET, POST, SERVER, SESSION)
- Use the Console for debugging your plugins and themes
- Use the Profiler for measuring the performance of your plugins and themes

== Installation ==

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Black Bar" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Black Bar plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I add entries to the Console? =

To add entries to the console, simply put the following line of PHP anywhere in your code:

`apply_filters( 'console', 'Enter something to debug here' );`

You can also add a specific log level like this:

`apply_filters( 'console', 'Enter something to debug here', 'error' );`

The available log levels are: `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, and `emergency`.

= How do I use the Profiler? =

To use the profiler, simply put the following line of PHP _before_ and _after_ the code you wish to profile:

`apply_filters( 'profile', 'Enter a description of what you are profiling here' );`

= How do I restrict the plugin to logged-in administrators? =

To restrict the plugin to logged-in administrators, use the following filter in your theme's functions.php file:

`/**
 * @return boolean
 */
add_filter( 'blackbar/enabled', function( $bool ) {
    $bool = is_user_logged_in()
        ? current_user_can( 'administrator' )
        : false;
    return $bool;
});`

== Changelog ==

= 2.2.1 (2019-03-11) =

- Fixed CSS styles

= 2.2.0 (2019-03-10) =

- Added ability to add a log level to the console (i.e. 'debug', 'warning', 'error', etc.)
- Fixed CSS styles

= 2.1.4 (2019-02-15) =

- Fixed CSS styles

= 2.1.3 (2019-01-28) =

- Updated plugin URL

= 2.1.2 (2019-01-18) =

- Fixed javascript error when [SAVEQUERIES](https://codex.wordpress.org/Debugging_in_WordPress#SAVEQUERIES) is not enabled

= 2.1.1 (2018-12-26) =

- Fixed code highlighting

= 2.1.0 (2018-09-08) =

- Added a "blackbar/enabled" filter hook which returns true or false. This can be used in your theme's functions.php file to restrict the blackbar from displaying for specific users (or non-logged-in users).

= 2.0.0 (2018-07-18) =

- Added a console tab (replaces the Errors tab, see the FAQ on how to use)
- Fixed SQL execution time filter
- Fixed miscellaneous styling issues
- Press ESC to close the blackbar panel
- Updated activation requirements check
- Updated minimum requirements to PHP 5.6/WP 4.7

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
