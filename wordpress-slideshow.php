<?php
/**
 * Plugin Name:     Wordpress SlideShow
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A slide show plugin with Add,Delete and sort Image feature
 * Author:          Shraddha Parashar
 * Text Domain:     wordpress-slideshow
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wordpress_Slideshow
 */

// Make sure we don't expose any info if called directly

if (!function_exists('add_action') ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
//define constant
define('SLIDESHOW_VERSION', '0.1.0');
define('SLIDESHOWY__MINIMUM_WP_VERSION', '1.0');
define('SLIDESHOW__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLIDESHOW_ASSETS_DIR', plugins_url('wordpress-slideshow'));
define('SLIDESHOW_DELETE_LIMIT', 100000);

//including and initializing the classes

	include_once SLIDESHOW__PLUGIN_DIR . '_inc/class.slideshow.php';
	include_once SLIDESHOW__PLUGIN_DIR . '_inc/class.slideshow-db.php';
	include_once SLIDESHOW__PLUGIN_DIR . '_inc/class.slideshow-front.php';
	include_once SLIDESHOW__PLUGIN_DIR . '_inc/class.slideshow-admin.php';
	include_once SLIDESHOW__PLUGIN_DIR . '_inc/class.slideshow-shortcode.php';
	add_action('init', array( 'Slideshow', 'init' ));
	add_action('init', array( 'Slideshow_Admin', 'init' ));
	add_action('init', array( 'Slideshow_Front', 'init' ));
	add_action('init', array( 'Slideshow_DB', 'init' ));
	add_action('init', array( 'Slideshow_Shortcode', 'init' ));
	//activation deactivation hooks
	register_activation_hook(__FILE__, array( 'Slideshow', 'plugin_activation' ));
	register_deactivation_hook(__FILE__, array( 'Slideshow', 'plugin_deactivation' ));
?>
