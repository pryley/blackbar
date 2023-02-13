=== Black Bar ===
Contributors: pryley, geminilabs
Donate link: https://ko-fi.com/pryley
Tags: blackbar, black bar, debug bar, debugbar, debugging, development, blackbox
Tested up to: 6.1
Stable tag: 4.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Black Bar is an unobtrusive Debug Bar for WordPress developers that attaches itself to the bottom of the browser window.

== Description ==

Black Bar is an unobtrusive Debug Bar for WordPress developers. It collects and displays errors, executed SQL queries, slow actions and hooks, theme templates, global variables, and provides a profiler.

How it helps you with development:

- Debug your code with the Console
- Inspect global variables (COOKIE, GET, POST, SERVER, SESSION, WP_Screen)
- Measure performance of your code with the Profiler
- View any PHP errors that occur when loading a page in the Console
- View executed MySQL queries along with execution time and backtrace
- View template files of the active theme in loaded order
- View the 50 slowest action and filter hooks along with callbacks ordered by priority

== Installation ==

If you have never installed a WordPress plugin before, you can [read instructions on how to do this here](https://wordpress.org/documentation/article/manage-plugins/).

== Frequently Asked Questions ==

= How do I add entries to the Console? =

To add entries to the console, insert the following line of PHP anywhere in your code:

`apply_filters('console', 'Enter something to debug here');`

You can also add an optional log level like this:

`apply_filters('console', 'Enter something to debug here', 'error');`

The available log levels are: `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, and `emergency`.

= How do I use the Profiler? =

To use the profiler, insert the following lines of PHP _before_ and _after_ the code you are profiling:

*Before:*

`apply_filters('trace:start', 'Enter a description of what you are profiling here');`

*After:*

`apply_filters('trace:stop');`

= How do I enable the plugin for non-administrators? =

By default, Black Bar is only visible to administrator users. To enable it for all logged-in users, add the following code to your child theme's functions.php file:

`add_filter('blackbar/enabled', 'is_user_logged_in');`

== Changelog ==

= 4.0.0 (2023-02-13) =

- Added console level filters
- Added sorting to Action/Filter Hooks
- Added syntax highlighting to console entries
- Added trace information to SQL queries
- Beautified SQL formating
- Changed Profiler usage (use the "trace:start" and "trace:stop" hooks)
- Improved Profiler, it is now also more accurate
- Refreshed UI
- Requires PHP >= 7.3

[See changelog for all versions](https://raw.githubusercontent.com/pryley/blackbar/main/changelog.txt).
