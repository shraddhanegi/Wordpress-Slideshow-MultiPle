<?php
/**
 * Class to create a slideshow shortcode dropdown on tinymce editor of post/pages
 *
 * @author <parashar.shraddha@gmail.com>
 */
class Test_Slideshow_Shortcode extends WP_Ajax_UnitTestCase
{

	/**
	 * Attache all the necessary function to
	 * respective hooks
	 *
	 * @return void
	 */
	public function test_init_hooks()
	{
		$test_wss_add_mce_button = has_action('admin_init', array('Slideshow_Shortcode','wss_add_mce_button'));
		$test_wss_shortcode_list_ajax = has_action( 'wp_ajax_wss_shortcode_list_ajax',  array('Slideshow_Shortcode','wss_shortcode_list_ajax') );
		//add_action( 'admin_footer', array('Slideshow_Shortcode','wss_shortcode_list') );
		$gallery_shortcode = ( $test_wss_add_mce_button === 10 && $test_wss_shortcode_list_ajax  === 10 );

		$this->assertTrue( $gallery_shortcode );
	}

}
