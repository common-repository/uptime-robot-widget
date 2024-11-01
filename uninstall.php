<?php
/**
 * Cleaning the database when uninstalling
 */

// Die if not called by WordPress
if(!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}

// Remove settings options
delete_option('uptimerobot_apikey');
delete_option('uptimerobot_show_ratio');
delete_option('uptimerobot_custom_period');
delete_option('uptimerobot_show_psp_link');
delete_option('uptimerobot_psp_url');
// Delete cache
delete_transient('uptimerobot_widget_cache');
