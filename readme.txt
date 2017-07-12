=== ATR Server Status ===
Contributors: Allan Thue Rehhoff, Nicklas Thomsen
Tags: server status, check server, service status, check service, server, status check
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 1.1.4
License: GPLv3

== Description ==
Simple, efficient, ad- and bloatfree plugin for testing whether or not a given server address is up for just you, or everyone else on a given port and protocol.
Servers & services are checked in real-time whenever a user requests to view the page where the shortcode is inserted.

Intuitive interface, makes is really easy to maintain servers & services to check.

You have the ability to filter/hook the message displayed to the user through functions.php in your theme folder.

`
add_filter("atr_server_success_message", function($message, $server) {
	return $server->humanname." appears to be working alright.";
}, 10, 2);

add_filter("atr_server_error_message", function($message, $server) {
	return $server->humanname." is down.";
}, 10, 2);
`

== Installation ==
1. Install the plugin
2. Configure the servers/services you want to check against in "Server Status" within wp-admin
3. Insert one of the provided shortcodes on the desired page, and or post.

== Screenshots ==
Screenshot 1: Administration screen for servers to be checked against
Screenshot 2: Example result of different servers

== Features ==
- Supports most common protocols (TCP, UDP, HTTP, HTTPS) (FTP is on the todo)
- Define a human friendly readable name for display
- Define hostname
- Define port
- Define timeout in seconds
- Define protocol
- Drag'n'drop ordering
- Edit and delete servers/services
- Shortcodes for checking one or more servers frontend

== Frequently Asked Questions ==
None yet.
If you have a question feel free to [send me an email](https://rehhoff.me/contact) and I'll do my best to answer you within 48 hours.

== Upgrade Notice ==
It is as always, recommended that you take a full backup of both your database and files, prior to updating plugins.

== Changelog ==
= 1.1.4 =
* More translateable strings
* Implemented a more responsive experience in backend
* Rephrased table heading descriptions to be a bit more descriptive

= 1.1.3 =
* Bugfix & security update.
* Fixed rare scenario where privilege escalation could occur during saving a servers data.
* Fixed CSRF vulnerabilities, when adding/editing/deleting server data.
* Rmove JS console.log breaking <= IE 7
* Some strings are now translateable (More to come in future releases)

= 1.1.2 =
* Added filters for messages displayed to the user.

= 1.1.1 =
* Added sanitization function.
* Now stripping sanitizing any data going into the database.

= 1.1.0 =
* Fixed bug where only 5 server would be displayed in backend
* Code Refactoring for publishing this plugin
* Added assets, and readme files.

= 1.0.0 =
* Initial release
