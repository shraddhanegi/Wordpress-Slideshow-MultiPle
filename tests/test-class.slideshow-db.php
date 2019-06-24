<?php
/**
 * Test class for validating gallery table
 * @package WordPress-Slideshow
 */
class Test_Slideshow_DB extends WP_Ajax_UnitTestCase {

	public function setUp() {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

	 /**
     * Helper to keep it DRY
     *
     * @param string $action Action.
     */
    protected function make_ajax_call( $action ) {
        // Make the request.
        try {
            $this->_handleAjax( $action );
        } catch ( WPAjaxDieContinueException $e ) {
            unset( $e );
        }
    }

	public function test_init_hooks()
    {
		$test_get_gallery_images = has_action('init', array( 'Slideshow_DB', 'get_gallery_images' ));
		$test_save_gallery_images = has_action('admin_init', array( 'Slideshow_DB', 'save_gallery_images' ));

		$gallery_db_registered = ( $test_get_gallery_images ===10 && $test_save_gallery_images=== 10 );

		$this->assertTrue( $gallery_db_registered );
    }

	/**
	 *
	 * test to fecth image from gallery
	 *
	 * @return void
	 */
	public function test_save_gallery_images(){
		global $wpdb;
		$_POST =  array(
            'action' => 'save_gallery_images',
            'security' => wp_create_nonce('rt-camp-slideshow'),
            'post_type' => 'post',
            'id' => '37'
        );
        $this->make_ajax_call( 'save_gallery_images' );
        // Get the results.
		$response = $this->_last_response;
        $this->assertEquals( 'success', $response );
	}

	/**
	 * test to save image to the gallery
	 *
	 *
	 * @return void
	 */
	public function test_get_gallery_images(){
		global $wpdb;
		// Check that a database table was added.
		$table_name = $wpdb->prefix . "gallery";
		$rtImagesObj = $wpdb->get_row(  "SELECT image_id FROM $table_name");
		$this->assertNull( $rtImagesObj);

	}
}
