<?php
/**
 * Class to create a slideshow menu for plugin on admin dashboard and it will contain
 * function to load all the resources required to
 *  generate the slideshow
 * @author <parashar.shraddha@gmail.com>
 */
class Test_Slideshow_Admin extends WP_UnitTestCase
{
	 /**
     * Attache all the necessary function to
     * respective hooks
     *
     * @return void
     */
    public function test_init_hooks()
	{
		//self::$initiated = true;
        //has_theme_support('post-thumbnails');
        $test_load_textdomain = has_action('admin_init', array( 'Slideshow_Admin', 'load_textdomain' ));
        $test_load_menu = has_action('admin_menu', array( 'Slideshow_Admin', 'load_menu' ));
		$test_load_resources = has_action('admin_enqueue_scripts', array( 'Slideshow_Admin', 'load_resources' ));
		$test_init_hook = ($test_load_textdomain === 10 && $test_load_menu && $test_load_resources);
		$this->assertTrue( $test_init_hook );
	  }
	  /**
	 * Function to load the text domain
	 *
	 * @return void
	 */
	public static function load_textdomain()
	{

		echo $load_domain = load_plugin_textdomain('wordpress-slideshow');
	}
	/**
	 *  function to load slideshpow menu
	 *
	 * @return void
	 */
	public function test_load_menu()
    {
		Slideshow_Admin::load_menu();
		$this->assertNotEmpty( menu_page_url( 'slideshow_image' ) );


	}

	 /**
     * Function to add the images to the gallery
     *
     * @return void
     */
    public function test_display_new_gallery()
    {
		global $wpdb;
		$test_table_name = $wpdb->prefix . "gallery";
		// $filename should be the path to a file in the upload directory.
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['url'].'/'.'glimse-img6.jpg';
		// The ID of the post this attachment is for.
		$parent_post_id = 37;

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
			if ($attach_data = wp_generate_attachment_metadata($attach_id, $filename)) {
				wp_update_attachment_metadata($attach_id, $attach_data);
			}

		set_post_thumbnail( $parent_post_id, $attach_id );
		$success = $wpdb->update(
			$test_table_name,
			array('image_id' => $attach_id ,'gallery_title' =>'Test Gallery'),
			array('gallery_id' =>1 ),
			array('%d','%s')
		);

		$inserted_dummy = ($success >0 );
		$this->assertTrue( $inserted_dummy );
	}
}
