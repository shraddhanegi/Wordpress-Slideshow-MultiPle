<?php
/**
 * Class to create a slideshow and it will contain
 * function to load all the resources required to
 *  generate the image slideshow
 *
 * @author <parashar.shraddha@gmail.com>
 */
class Slideshow
{

    private static $initiated = false;
    public static function init()
    {
        if (! self::$initiated ) {
            self::init_hooks();
        }
    }
    /**
     * Initializes WordPress hooks
     */
    public static function init_hooks()
    {
		self::$initiated = true;
    }


    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     */
    public static function plugin_activation()
    {
		global $wpdb;

        load_plugin_textdomain('wordpress-slideshow');

      	$table_name = $wpdb->prefix . "gallery";
        $rt_gallery_db_version = '1.0.0';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {
          		  $sql = "CREATE TABLE $table_name (
									`gallery_id` int (11) NOT NULL AUTO_INCREMENT,
									`image_id` varchar(200),
									`gallery_title` varchar(200),
									`shortcode` varchar(200),
									PRIMARY KEY  (gallery_id)
					)    $charset_collate;";
             include_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$delta = dbDelta($sql);
            add_option('rt_db_version', $rt_gallery_db_version);
        }
		flush_rewrite_rules();
    }


    /**
     * Removes all connection options
     *
     * @static
     */
    public static function plugin_deactivation( )
    {
		delete_option('rt_db_version');
		flush_rewrite_rules();

    }

}
