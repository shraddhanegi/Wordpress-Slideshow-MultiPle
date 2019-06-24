<?php
/**
 * Test class for validating shortcode [slideshow]
 * @package WordPress-Slideshow
 */
class Test_Slideshow_Front extends WP_UnitTestCase {

	public function test_init_hook(){
		$flex_slider_register_scripts = has_action('wp_print_scripts', array('Slideshow_Front', 'fs_register_scripts'));
		$flex_slider_register_style = has_action('wp_print_styles', array('Slideshow_Front', 'fs_register_styles'));
		$flex_slider_slideshow_template = has_action('init', array( 'Slideshow_Front', 'slideshow_template' ));
		$flex_slider_slideshow_shortcode = has_action('init', array( 'Slideshow_Front', 'slideshow_shortcode' ));

		$action_registered = ($flex_slider_register_scripts ===10 && $flex_slider_register_style === 10 &&  $flex_slider_slideshow_template &&  $flex_slider_slideshow_shortcode);

		$this->assertTrue( $action_registered );

	}

	/**
	 * Test for registered script of flexslider
	 * on frontend
	 * @return void
	 */
	public function test_fs_register_scripts()
    {

        // setup admin screen to make is_admin() as true
			$this->assertFalse(is_admin());

			Slideshow_Front::fs_register_scripts();
			// register
			$this->assertTrue( wp_script_is( 'JqueryFlexSlider', 'registered' ) );
			$this->assertTrue( wp_script_is( 'slideshow', 'registered' ) );
			//enqueue
			$this->assertTrue( wp_script_is( 'JqueryFlexSlider', 'enqueued' ) );
			$this->assertTrue( wp_script_is( 'slideshow', 'enqueued' ) );


			// revert back the screen
			$current_screen = null;
	}

	/*
    * function to enqueue flexslider style at frontend
    */
    public function test_fs_register_styles()
    {
        // setup admin screen to make is_admin() as true
			$this->assertFalse(is_admin());
			Slideshow_Front::fs_register_styles();
			// register
			$this->assertTrue( wp_style_is( 'FlexSlider', 'registered' ) );
			// enqueue
			$this->assertTrue( wp_style_is( 'FlexSlider', 'enqueued' ) );


	}
	/**
     * Test for slideshow template
     *
     * @return void
     */
    public function test_slideshow_template()
    {

		$no_image_test = 'Admin section';
		ob_start();
		Slideshow_Front::slideshow_template();
		$sortable_gallery = Slideshow_DB::get_gallery_images();
		$this->assertFalse(!empty($sortable_gallery) ? true : false);
		$slider_test = ob_get_clean();
		$pos = strstr($slider_test,$no_image_test);
		$result = ($pos == $no_image_test);
		$this->assertTrue($result);
		/*$this->expectOutputString($no_image_test);*/
	}


}
