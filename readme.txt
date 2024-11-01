=== Uptime Robot Widget ===
Contributors: Beherit
Tags: uptime robot, uptime, uptimerobot, widget, monitoring, uptime monitoring, server monitoring,
Requires at least: 4.6
Tested up to: 5.4
Stable tag: 1.8.2
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple widget that shows the status of the monitored services in the Uptime Robot service.

== Description ==

A simple widget that shows the status of the monitored services in the Uptime Robot service. You only need to enter your Uptime Robot API key and add widget to website.

== Installation ==

In most cases you can install automatically from plugins page in admin panel.

However, if you want to install it manually, follow these steps:

1. Download the plugin and unzip the archive.
2. Upload the entire `uptime-robot-widget` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress.

== Changelog ==
= 1.8.2 (2020-05-08) =
* Minor cache improvements.
= 1.8 (2020-04-10) =
* Visual changes on the settings page.
* Removed unused options.
* Getting from API the list of public status pages.
* Localize cache for multi language support.
* Pure JavaScript instead of jQuery.
= 1.7 (2019-09-26) =
* Using REST API instead of Ajax for loading widget.
* Improved cache logic.
* Visual changes on the settings page.
* Added API key test.
* Remove FontAwesome.
* Minor fix in default CSS.
= 1.6.2 (2017-02-18) =
* Minor bug fixes.
= 1.6 (2017-02-11) =
* Switch to APIv2.
* Minor improvements.
= 1.5 (2016-12-19) =
* Public status page support.
* Deleting all data after uninstalling the plugin.
* Visual changes on the settings page.
* FontAwesome updated to 4.7.0.
* Other minor improvements.
= 1.4 (2016-07-30) =
* Added cache system.
* Better connection errors handling.
* Removed retrying the connection in Ajax requests.
* FontAwesome updated to 4.6.3.
* Other minor improvements.
= 1.3.4 (2016-04-05) =
* Added retrying the connection in get data.
* Fix widget constructor.
* Minor changes and fixes.
= 1.3.2 (2016-03-12) =
* Add POT file and remove language files to allow WordPress.org language packs to take effect.
= 1.3 (2016-03-06) =
* Ability to get uptime ratio in a custom period.
* FontAwesome updated to 4.5.0.
= 1.2.2 (2015-12-17) =
* Return data in json at ajax request.
* Changed the default timeout options value.
= 1.2 (2015-10-21) =
* Added new options - connection timeout and connection retry limit.
* Added retrying retrieve data in case of an error.
* Changed language domain to uptime-robot-widget to work with WordPress new translation process.
* Other changes and fixes.
= 1.1.3 (2015-08-08) =
* Use PHP5 object constructors.
= 1.1.2 (2015-08-04) =
* Updated FontAwesome.
* Minor bugfix.
= 1.1 (2015-06-21) =
* Optimize linking the scripts.
* Minify jQuery script.
* Properly added a link to the settings on plugins page.
= 1.0.2 (2015-05-28) =
* Minor changes and fixes.
= 1.0 (2015-05-25) =
* First public version.