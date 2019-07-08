<?php
/**
 * Class to create a slideshow shortcode dropdown on tinymce editor of post/pages
 *
 * @author <parashar.shraddha@gmail.com>
 */
class Slideshow_Shortcode
{
	private static $initiated = false;

	/**
	 * Function to initialise the hooks of the plugin
	 *
	 * @return void
	 */
	public static function init()
	{
		if (!self::$initiated) {
			self::init_hooks();
		}
	}
	/**
	 * Attache all the necessary function to
	 * respective hooks
	 *
	 * @return void
	 */
	public static function init_hooks()
	{
		self::$initiated = true;

		add_action('admin_init', array('Slideshow_Shortcode','wss_add_mce_button'));
		add_action( 'wp_ajax_wss_shortcode_list_ajax',  array('Slideshow_Shortcode','wss_shortcode_list_ajax') );
		//add_action( 'admin_footer', array('Slideshow_Shortcode','wss_shortcode_list') );
	}
	/**
	 * Function to add dropdown list of short code on visual editor
	 *
	 * @return void
	 */
	public static function wss_add_mce_button() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
				   return;
		   }
	   // check if WYSIWYG is enabled
	   if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_buttons', array('Slideshow_Shortcode','wss_register_mce_button') );
			add_filter( 'mce_external_plugins', array('Slideshow_Shortcode','wss_add_tinymce_plugin') );



		   }
	}

	/**
	 * Fetch all the shortcodes and its title from gallery
	 *
	 */
	/**
	 * Function to fetch all the shortcode and its title
	 *
	 * @return string
	 */


	public function wss_shortcode_list_ajax() {
		// check for nonce
		//check_ajax_referer( 'wss-nonce', 'security' );
		$galleryInfo = Slideshow_DB::get_all_gallery();
		 $galleryList = array();
		foreach($galleryInfo as $gallery){
			$galleryList[] =array(
				'gallery_title' => $gallery->gallery_title,
				'shortcode' =>  $gallery->shortcode
			);
		}
		return wp_send_json( $galleryList );

	}

	/**
	 * register new button in the tinymce editor
	 *
	 * @param [type] $buttons
	 * @return $buttons
	 */
	public static function wss_register_mce_button( $buttons ) {
		array_push( $buttons, 'wss_mce_dropbutton' );
		return $buttons;
	}

	/**
	 * Declare a script for the new button
	 * the script will insert the shortcode on the click event
	 *
	 * @param [type] $plugin_array
	 * @return $plugin_array
	 */
	public static function wss_add_tinymce_plugin( $plugin_array ) {

		$plugin_array['wss_mce_dropbutton'] = SLIDESHOW_ASSETS_DIR . '/asset/js/wss_mce_dropbutton.js';
		return $plugin_array;
	}
}
