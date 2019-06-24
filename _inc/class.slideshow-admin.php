<?php
/**
 * Class to create a slideshow menu for plugin on admin dashboard and it will contain
 * function to load all the resources required to
 *  generate the slideshow
 * @author <parashar.shraddha@gmail.com>
 */
class Slideshow_Admin
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

		add_action('admin_init', array('Slideshow_Admin', 'load_textdomain'));
		add_action('admin_menu', array('Slideshow_Admin', 'load_menu'));
		add_action('admin_enqueue_scripts', array('Slideshow_Admin', 'load_resources'));
		add_action('admin_init', array('Slideshow_Admin', 'add_new_image_sizes_slideshow'));

		add_action('wp_ajax_save_gallery_images', array('Slideshow_Admin', 'save_gallery'));
		add_action('wp_ajax_nopriv_save_gallery_images', array('Slideshow_Admin', 'save_gallery'));
		add_action('wp_ajax_delete_gallery', array('Slideshow_Admin', 'delete_gallery'));
		add_action('wp_ajax_nopriv_delete_gallery', array('Slideshow_Admin', 'delete_gallery'));
		add_action('wp_ajax_add_new_gallery', array('Slideshow_Admin', 'add_new_gallery'));
		add_action('wp_ajax_nopriv_add_new_gallery', array('Slideshow_Admin', 'add_new_gallery'));


	}
	/**
	 * Function to load the text domain
	 *
	 * @return void
	 */
	public static function load_textdomain()
	{

		load_plugin_textdomain('wordpress-slideshow');
	}
	/**
	 * Function to add Gallery menu to the admin Dashboard
	 *
	 * @return void
	 */
	public static function load_menu()
	{

		add_menu_page(__('Slider Images', 'wordpress-slideshow'), __(' Slideshow', 'wordpress-slideshow'), 'manage_options', 'slideshow_image', array('Slideshow_Admin', 'display_new_gallery'));
	}
		/**
	 * Function to add the images to the gallery
	 *
	 * @return void
	 */

	public static function display_new_gallery()
	{
		// Get the slide from db table
		$sortable_gallery_array = Slideshow_DB::get_all_gallery();
		?>
		<main role="main" class="container">
		<h3> <?php _e('Slider Images', 'wordpress-slideshow'); ?></h3>
		<?php
		$output = '<div id="rtgallery">';
			//looping through gallery array
			if(empty($sortable_gallery_array)) {
				echo '<div class="alert alert-info">Please Insert image to the garllery</div>';

			} else {
				foreach( $sortable_gallery_array as $sortable_gallery ) {
					// Getting all the image IDs by creating an array from string ( 1,3,5 => array( 1, 3, 5) )
					$gallery = explode(",", $sortable_gallery->image_id);
					$output .= '<div class="of-repeat-group jumbotron" >';
					$output .= '<span class="'.$sortable_gallery->gallery_id.'_msgbox"></span>';
					$output.= '<div class="inputTitle"><input type="text" value="'.$sortable_gallery->gallery_title.'" name="galleryTitle" id="'.$sortable_gallery->gallery_id.'_gallery_title"></div>';
					//Creating a dynamic image gallery to display on plugin menu /
					$output .= '<ul id="'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery" data-id="'.$sortable_gallery->gallery_id.'" class="sortable_wordpress_gallery">';

					// If there is any ID, create the image for it
					if (count($gallery) > 0 && $gallery[0] != '') {

						foreach ($gallery as $attachment_id) {
							// Create a LI elememnt
							$output .= '<li tabindex="0" role="checkbox" aria-label="' . get_the_title($attachment_id) . '" aria-checked="true" data-id="' . $attachment_id . '" class="attachment save-ready selected details">';
							// Create a container for the image. (Copied from the WP Media Library Modal to use the same styling)
							$output .= '<div class="attachment-preview js--select-attachment type-image subtype-jpeg">';
							$output .= '<div class="thumbnail">';

							$output .= '<div class="centered">'.$attachment_id;
							// Get the URL to that image thumbnail
							$output .= '<img src="' . wp_get_attachment_image_src($attachment_id)[0] . '" width="150" height="150" class="img-thumbnail" draggable="false" alt="' . get_the_title($attachment_id) . '">';

							$output .= '</div>';

							$output .= '</div>';

							$output .= '</div>';
							// Add the button to remove this image if wanted (we set the data-gallery to target the correct gallery if there are more than one)
							$output .= '<button type="button" data-gallery="#' . $sortable_gallery->gallery_id . '_sortable_wordpress_gallery"  class="button-link check remove-sortable-wordpress-gallery-image"   tabindex="0">
												<span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>';

							$output .= '</li>';

						}//end of foreach
					}//end of if
					else{
						$output .= '<li tabindex="0" role="checkbox" aria-label="' . get_the_title($attachment_id) . '" aria-checked="true" data-id="' . $attachment_id . '" class="attachment save-ready selected details">';
							// Create a container for the image. (Copied from the WP Media Library Modal to use the same styling)
							$output .= '<div class="attachment-preview js--select-attachment type-image subtype-jpeg">';
							$output .= '<div class="thumbnail">';

							$output .= '<div class="centered">';
							$output .= '<img src="' . SLIDESHOW_ASSETS_DIR .'/asset/images/noimageavailable.png"  width="150" height="150" class="img-thumbnail" draggable="false" alt="' . get_the_title($attachment_id) . '">';
							$output .= '</div>';

							$output .= '</div>';

							$output .= '</div>';
							$output .= '</li>';
					}
					$output .= '</ul>';
					$output .= '<button class="dodelete button button-primary icon delete" data-gallery-id = "'.$sortable_gallery->gallery_id.'">Remove Gallery </button>';

					// Hidden input used to save the gallery image IDs into the database
					//We are also creating dynamic IDs here so that we can easily target them in JavaScript
					$output .= '<input type="hidden" id="'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery_input" name="_'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery" value="'.$sortable_gallery->image_id.'" />';
					// Button used to open the WordPress Media Library Modal
					$output .= '<button type="button" class="button button-primary add-sortable-wordpress-gallery" data-gallery="#'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery" data-gallery-id = "'.$sortable_gallery->gallery_id.'" id= "'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery_add">Add Images</button>';
					$output .= '<button  type="button" class="button button-primary  save-sortable-wordpress-gallery" data-gallery="#'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery" data-gallery-id = "'.$sortable_gallery->gallery_id.'" id= "'.$sortable_gallery->gallery_id.'_sortable_wordpress_gallery_save">Save Images</button>';
					$output .= '</div>';
				} //end of foreach loop
			}
			// add hidde gallery
			$output .= '<button class="docopy button-primary">Add Gallery</button>';
			$output .= '<div class="of-repeat-group jumbotron to-copy">';
			$output .= '<span class="msgbox"></span>';
			$output.= '<div class="inputTitle"><input type="text" value="" name="galleryTitle" ></div>';

			/*Creating a dynamic image gallery to display on plugin menu*/
			$output .= '<ul id="sortable_wordpress_gallery" class="sortable_wordpress_gallery ui-sortable">';

			// Getting all the image IDs by creating an array from string ( 1,3,5 => array( 1, 3, 5) )

			// Create a LI elememnt
			$output .= '<li tabindex="0" role="checkbox" aria-label="" aria-checked="true" data-id="" class="attachment save-ready selected details">';
			// Create a container for the image. (Copied from the WP Media Library Modal to use the same styling)
			$output .= '<div class="attachment-preview js--select-attachment type-image subtype-jpeg">';
			$output .= '<div class="thumbnail">';

			$output .= '<div class="centered">';
			// Get the URL to that image thumbnail
			$output .= '<img src="'.SLIDESHOW_ASSETS_DIR .'/asset/images/noimageavailable.png" width="150" height="150" class="img-thumbnail" draggable="false" alt="">';
			$output .= '</div>';

			$output .= '</div>';

			$output .= '</div>';
			// Add the button to remove this image if wanted (we set the data-gallery to target the correct gallery if there are more than one)
			$output .= '<button type="button" data-gallery="sortable_wordpress_gallery" class="button-link check remove-sortable-wordpress-gallery-image"   tabindex="0">
												<span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>';

			$output .= '</li>';
			$output .= '</ul>';
			$output .= '<button class="dodelete button button-primary icon delete">Remove Gallery </button>';

			// Hidden input used to save the gallery image IDs into the database
			//We are also creating dynamic IDs here so that we can easily target them in JavaScript
			$output .= '<input type="hidden" id="sortable_wordpress_gallery_input" name="sortable_wordpress_gallery_input" data-gallery="sortable_wordpress_gallery_input" value="" />';
			// Button used to open the WordPress Media Library Modal
			$output .= '<button type="button" class="button button-primary add-sortable-wordpress-gallery" id="sortable_wordpress_gallery_add">Add Images</button>';
			$output .= '<button  type="button" class="button button-primary  save-sortable-wordpress-gallery" data-gallery="sortable_wordpress_gallery" id="sortable_wordpress_gallery_save" >Save Images</button>';
			$output .= '</div>';


			$output .= '</div>';
			echo $output; ?>
	</main>
	<?php
	}
	/**
	 *
	 * Function to genrate image size for slider
	 *
	 * @return void
	 */
	public static function add_new_image_sizes_slideshow()
	{
		add_image_size('fxthumbnails', 150, 150, true);
		add_image_size('fxfull', 800, 408, true);

	}


	/**
	 * Function to Attach resources file to
	 * build the gallery at admin dashboard on click of Sldieshow menu
	 *
	 * @return void
	 */
	public static function load_resources()
	{

		/*global $hook_suffix; */
		if (isset($_GET['page']) && $_GET['page'] == 'slideshow_image') {
			wp_enqueue_script('media-upload');
			wp_enqueue_media();

			wp_register_style('BootstrapCss', SLIDESHOW_ASSETS_DIR . '/lib/bootstrap/bootstrap.min.css', array(), '4.0.0');
			wp_enqueue_style('BootstrapCss');
			wp_register_style('GalleryCss', SLIDESHOW_ASSETS_DIR . '/asset/css/gallery.css', array(), SLIDESHOW_VERSION);
			wp_enqueue_style('GalleryCss');
			wp_register_script('RepeatJs', SLIDESHOW_ASSETS_DIR . '/asset/js/repeat.js', array('jquery'), SLIDESHOW_VERSION);
			wp_enqueue_script('RepeatJs');
			wp_register_script('SortableJs', SLIDESHOW_ASSETS_DIR . '/asset/js/sortable.js', array('jquery'), SLIDESHOW_VERSION);
			wp_enqueue_script('SortableJs');
			wp_register_script('GalleryJs', SLIDESHOW_ASSETS_DIR . '/asset/js/gallery.js', array('jquery'), SLIDESHOW_VERSION);
			wp_enqueue_script('GalleryJs');
			wp_register_script('ajaxhandle', SLIDESHOW_ASSETS_DIR . '/asset/js/update.js', array('jquery'), SLIDESHOW_VERSION);
			wp_enqueue_script('ajaxhandle');
			wp_localize_script('ajaxhandle', 'ajax_custom', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce('rt-camp-slideshow')
			));

			wp_register_script('BootstrapJs', SLIDESHOW_ASSETS_DIR . '/lib/bootstrap/bootstrap.min.js', array('jquery'), '4.0.0');
			wp_enqueue_script('BootstrapJs');
		}
	}
}
