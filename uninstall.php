<?php
/**
 * Uninstall file for the plugin
 * This magic file is run automatically when the users deletes the plugin
 *
 * @package WordPress-Slideshow
 */
global $wpdb;
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}
$table_name = $wpdb->prefix . "gallery";
if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name ) {
				$wpdb->query("DROP TABLE IF EXISTS $table_name");
}
delete_option('rt_db_version');
add_shortcode( 'slideshow', '__return_false' );
