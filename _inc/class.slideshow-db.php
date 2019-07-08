<?php
/**
 * Class to add database functionality to
 * to the plugin it contain required  functions
 * for insert and update the images to the databse
 *
 */

class Slideshow_DB
{

    private static $initiated = false;
    /**
     * Inititalise the Slidehow DB class
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
     * Initializes WordPress hooks
     *
     * @return void
     */
    public static function init_hooks()
    {
		self::$initiated = true;

		add_action('init', array( 'Slideshow_DB', 'get_all_gallery' ));
		add_action('admin_init', array( 'Slideshow_DB', 'save_gallery' ));
		add_action('admin_init', array( 'Slideshow_DB', 'delete_gallery' ));
		add_action('admin_init', array( 'Slideshow_DB', 'add_new_gallery' ));

    }

    /**
     * Fetch all the gallery images
     *
     * @return $img_id
     */
    public static function get_all_gallery()
    {
		global $wpdb;
        $table_name = $wpdb->prefix . "gallery";
		$rtImagesObj = $wpdb->get_results( "SELECT image_id,gallery_id,gallery_title,shortcode FROM $table_name");

		if(!empty($rtImagesObj)){
			foreach ( $rtImagesObj as $rtkey => $rtObject){
				$gallery[$rtkey] = $rtObject;
			}
		}
		return $gallery;

	}
	/**
	 * get gallery by shortcode ID
	 *
	 * @return void
	 */
	public static function get_gallery_by_id($id){
		global $wpdb;
        $table_name = $wpdb->prefix . "gallery";
		$rtImagesObj = $wpdb->get_results( "SELECT image_id,gallery_id,gallery_title FROM $table_name where gallery_id=$id");

		if(!empty($rtImagesObj)){
			foreach ( $rtImagesObj as $rtkey => $rtObject){
				$gallery[$rtkey] = $rtObject;
			}
		}
		return $gallery;


	}
	/**
	 * This Function will execute when user will
	 * add new gallery
	 * @return $galleryID
	 */
	public static function add_new_gallery(){
		global $wpdb;

		$table_name = $wpdb->prefix . "gallery";
		if( isset($_POST['action']) && $_POST['action'] == 'add_new_gallery')
		{
			$rtObject = $wpdb->get_row("select MAX( gallery_id ) + 1 as newGalleryID  from $table_name");
			$newGalleryID = $rtObject->newGalleryID;
			$shortcode = "[slideshow id='".$newGalleryID."']";
			$data = array('gallery_id' => $newGalleryID,
							'shortcode' => $shortcode);
			$format = array('%d','%s');
			$wpdb->insert($table_name,$data,$format);
			if($wpdb->insert_id >0){
				$newGalleryID = $wpdb->insert_id;
			}
			else{
				$newGalleryID = 0;
			}
			echo $newGalleryID; die;
		}
	}

    /**
     * Save selected images from media uploader
     *
     * @return $success
     */
    public static function save_gallery()
    {
        global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		if( isset($_POST['action']) && $_POST['action'] == 'save_gallery')
		{
			//check_ajax_referer( 'rt-camp-slideshow', 'security' );
		$imageID = (isset($_POST['imageID'])?$_POST['imageID'] : '');   //get image id from ajax post data
		$galleryID = (isset($_POST['galleryID'])?$_POST['galleryID'] : ''); //get gallery id ajax post data
		$galleryTitle = (isset($_POST['galleryTitle'])?$_POST['galleryTitle'] : '');
		//case : update gallery
        if (isset($galleryID)) {
			//fetch gallery_id from gallery table
            $results = $wpdb->get_results("select gallery_id from $table_name where gallery_id = $galleryID");
//print_r($results);
			foreach ($results as $res) {
				$resGalleryId = $res->gallery_id; //get imageid from gallery table
            }
			//check if already exisited in gallery table and galleryid is same than  update imageIDS
            if (!empty($resGalleryId) && $resGalleryId == $galleryID ) {
				$shortcode = "[slideshow id=\"$galleryID\"]";
                $success = $wpdb->update(
                    $table_name,
                    array(
						'image_id' => $imageID,
						'gallery_title' => $galleryTitle,
						'shortcode' =>$shortcode
                    ),
                    array('gallery_id' => $galleryID)
                );

			}

            if (isset($success) && $success >0 ) {
                $success = 'success';

            } else {
                $success = 'failed';

			}
            echo $success; wp_die();
        }

		}

	}
	/**
	 * Delete gallery from slideshow section
	 * @return $success
	 */

	 public static function delete_gallery(){
		global $wpdb;
		if( isset($_POST['action']) && $_POST['action'] == 'delete_gallery')
		{
			$table_name = $wpdb->prefix . "gallery";
			$galleryID = $_POST["galleryID"]; //get gallery id ajax post data
		  //case delete gallery
		  if (isset($galleryID)) {
			  //delete row releted to gallery_id from gallery table
			  $results = $wpdb->delete( $table_name , array('gallery_id' => $galleryID), array( '%d' ) );
			  if ($results >0 ) {
				  $success = 'success';

			  } else {
				  $success = 'failed';

			  }
			  echo $success;
			  wp_die();

		  }
  }

	 }
}
