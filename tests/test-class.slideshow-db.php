<?php
/**
 * Test class for validating gallery table
 * @package WordPress-Slideshow
 */
class Test_Slideshow_DB extends WP_Ajax_UnitTestCase {

	/**
	 * funtion for ajax method
	 *
	 * @return void
	 */
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

	/**
	 * test to check Attache all the necessary action to respective hooks
	 *
	 * @return void
	 */
	public function test_init_hooks()
    {
		 $test_add_new_gallery = has_action('admin_init', array( 'Slideshow_DB', 'add_new_gallery' ));
		 $test_get_all_gallery = has_action('init', array( 'Slideshow_DB', 'get_all_gallery' ));
		 $test_save_gallery = has_action('admin_init', array( 'Slideshow_DB', 'save_gallery' ));
		 $test_delete_gallery = has_action('admin_init', array( 'Slideshow_DB', 'delete_gallery' ));

		$gallery_db_registered = ( $test_get_all_gallery === 10 && $test_save_gallery  === 10 && $test_delete_gallery === 10 &&  $test_add_new_gallery === 10);

		$this->assertTrue( $gallery_db_registered );
	}

	/**
	* This Function will execute when user will
	* add new gallery
	* @return $galleryID
	*/
	public function test_add_new_gallery(){
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		$rtObject = $wpdb->get_row("select MAX( gallery_id ) + 1 as newGalleryID  from $table_name");
		$newGalleryID = $rtObject->newGalleryID;
		$newGalleryID = (isset($newGalleryID)? $newGalleryID : 1);
		$shortcode = "[slideshow id='".$newGalleryID."']";
		$data = array('gallery_id' => $newGalleryID,
						'shortcode' => $shortcode);
		$format = array('%d','%s');

		$wpdb->insert($table_name,$data,$format);

		$inserted = $wpdb->insert_id;
		if(is_numeric($inserted))
			$result =true;
		else
			$result = false;

		$this->assertTrue( $result);
	}

	/**
	 *
	 * test to save gallery to the gallery table
	 *
	 * @return void
	 */
	public function test_save_gallery(){
		global $wpdb;
		$_POST =  array(
            'action' => 'save_gallery',
            'post_type' => 'post',
            'galleryID' => '1'
        );
        $this->make_ajax_call( 'save_gallery' );
		// Get the results.
		$response = $this->_last_response;

        $this->assertEquals('success', $response );
	}

	/**
	 * test to get gallery from the database
	 *
	 *
	 * @return void
	 */
	public function test_get_all_gallery(){
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		$rtImagesObj = $wpdb->get_row(  "SELECT gallery_id FROM $table_name");
		$this->assertEquals(1, $rtImagesObj->gallery_id);

	}

	/**
	 * test to get gallery by shortcode ID
	 *
	 * @return void
	 */
	public function test_get_gallery_by_id( $id=1 ){
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		$rtImagesObj = $wpdb->get_results( "SELECT image_id,gallery_id,gallery_title FROM $table_name where gallery_id = $id");

		$this->assertEquals(1, $rtImagesObj[0]->gallery_id);
	}


}
