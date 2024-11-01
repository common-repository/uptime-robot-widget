<?php
/**
 * Plugin Name: Uptime Robot Widget
 * Plugin URI: https://beherit.pl/en/wordpress/uptime-robot-widget/
 * Description: Adds a widget that shows the status of the monitored services in the Uptime Robot service.
 * Version: 1.8.2
 * Requires at least: 4.6
 * Requires PHP: 7.0
 * Author: Krzysztof Grochocki
 * Author URI: https://beherit.pl/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: uptime-robot-widget
 */

if(!defined('ABSPATH')) {
	exit;
}

// Define variables
define('UPTIME_ROBOT_WIDGET_VERSION', '1.8.2');
define('UPTIME_ROBOT_WIDGET_BASENAME', plugin_basename(__FILE__));
define('UPTIME_ROBOT_WIDGET_DIR_PATH', plugin_dir_path(__FILE__));
define('UPTIME_ROBOT_WIDGET_DIR_URL', plugin_dir_url(__FILE__));

// Load necessary files
require_once UPTIME_ROBOT_WIDGET_DIR_PATH.'includes/settings.php';
require_once UPTIME_ROBOT_WIDGET_DIR_PATH.'includes/widget.php';
