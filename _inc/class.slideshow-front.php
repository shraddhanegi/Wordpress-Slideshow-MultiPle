<?php
/**
 * Class to display slide show at front end using shortcode
 * [slideshow] it has functionality to attach the required
 * css and js and functionality to
 * show flexslider at wodpress page/post
 *
 * @author <parashar.shraddhagmail.com>
 *
 */
class Slideshow_Front
{

    private static $initiated = false;


    /**
     * Attached to enqueue script at front end
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
        add_action('wp_print_scripts', array('Slideshow_Front', 'fs_register_scripts'));
		add_action('wp_print_styles', array('Slideshow_Front', 'fs_register_styles'));
		add_action('init', array('Slideshow_Front', 'fxfull_get_attachment_id_by_url'));
		add_action('init', array( 'Slideshow_Front', 'slideshow_template' ));
		add_action('init', array( 'Slideshow_Front', 'slideshow_shortcode' ));
	}

    /**
     * Attached necessary script and style at frontend for flexslider
     *
     * @return void
     *
     */
    public static function fs_register_scripts()
    {

        if (!is_admin()) {
            // register
            wp_register_script('JqueryFlexSlider', SLIDESHOW_ASSETS_DIR . '/lib/flexslider/jquery.flexslider-min.js', array('jquery'), SLIDESHOW_VERSION);
            wp_register_script('Slideshow', SLIDESHOW_ASSETS_DIR . '/lib/flexslider/slideshow.js', array('jquery'), SLIDESHOW_VERSION);
			wp_register_script('LazyLoadMin', SLIDESHOW_ASSETS_DIR . '/lib/lazy/jquery.lazy.min.js', array('jquery'), SLIDESHOW_VERSION);
			wp_register_script('LazyLoad', SLIDESHOW_ASSETS_DIR . '/lib/lazy/lazyload.js', array('jquery'), SLIDESHOW_VERSION);

            // enqueue
            wp_enqueue_script('JqueryFlexSlider');
			wp_enqueue_script('Slideshow');
			wp_enqueue_script('LazyLoadMin');
			wp_enqueue_script('LazyLoad');
        }
    }

    /*
    * function to enqueue style at front end
    */
    public static function fs_register_styles()
    {
        if (!is_admin()) {
            // register
            wp_register_style('FlexSlider', SLIDESHOW_ASSETS_DIR . '/lib/flexslider/flexslider.css', array(), SLIDESHOW_VERSION);
            // enqueue
			wp_enqueue_style('FlexSlider');
			 // register
			wp_register_style('LazyLoad', SLIDESHOW_ASSETS_DIR . '/lib/lazy/lazyload.css', array(), SLIDESHOW_VERSION);
            // enqueue
            wp_enqueue_style('LazyLoad');
        }
    }

	public static function fxfull_get_attachment_id_by_url( $url ) {

		// Split the $url into two parts with the wp-content directory as the separator
		$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		// Get the host of the current site and the host of the $url, ignoring www
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

		// Return nothing if there aren't any $url parts or if the current host and $url host do not match
		if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
			return;
		}

		// Now we're going to quickly search the DB for any attachment GUID with a partial path match

		// Example: /uploads/2013/05/test-image.jpg
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

		// Returns null if no attachment is found
		return $attachment[0];
	}
    /**
     * Attached to create slideshow template
     *
     * @return void
     */
    public static function slideshow_template($id)
    {
        // Get the gallery images from db table
		$sortable_gallery_array = Slideshow_DB::get_gallery_by_id($id);
		if (!empty($sortable_gallery_array)) {
        ?>
    <div class="flexslider">
        <ul class="slides">

            <?php
            // Getting all the image IDs by creating an array from string ( 1,3,5 => array( 1, 3, 5) )
			foreach( $sortable_gallery_array as $sortable_gallery ) {
                $gallery = explode(",", $sortable_gallery->image_id);

                // If there is any ID, create the image for it
                if (count($gallery) > 0 && $gallery[0] != '') {
                    foreach ($gallery as $attachment_id) {
                        ?>
                        <li>


                            <a href="<?php echo wp_get_attachment_url( $attachment_id,'fxfull',false,false); ?>">
							<?php // Check if there's a Slide URL given and if so let's a link to it
						 $fxfull_image = wp_get_attachment_image_src( Slideshow_Front::fxfull_get_attachment_id_by_url(wp_get_attachment_url( $attachment_id,array(600,400))), 'fxfull');


						 ?>
							   <?php
								// The Slide's Image
                                echo '<img class="lazy" src="' . $fxfull_image[0]. '"  data-src="' . $fxfull_image[0] . '"  alt="' . get_the_title($attachment_id) . '">';
                                ?>
                            </a>

                        </li>
                        <?php }
				}
			}
             ?>
            </ul>
            <!--.slides -->
        </div>
        <!--.flexslider -->

    <?php
	}
	else {
		echo 'Please add images to slideshow from Admin section';
	}
}
    /**
     * Function to return shortcode for slider
     *
     * @return $slider
     */
    public static function slideshow_shortcode($atts)
    {
		extract( shortcode_atts( array(
			'id' => '',
		), $atts, 'slideshow' ) );
        ob_start();
        Slideshow_Front::slideshow_template($id);
        $slider = ob_get_clean();
        return $slider;
    }
}
add_shortcode('slideshow', array(new Slideshow_Front, 'slideshow_shortcode'));
?>
