<?php
/**
 *
 */
class Test_Slideshow extends WP_UnitTestCase {

	//
	// Protected properties.
	//

	/**
	 * The full path to the main plugin file.
	 *
	 * @type string $plugin_file
	 */
	protected $plugin_file;

	//
	// Public methods.
	//

	/**
	 * Set up for the tests.
	 */
	public function setUp() {

		// You must set the path to your plugin here.
		// This should be the path relative to the plugin directory on the test site.
		// You will need to copy or symlink your plugin's folder there if it isn't
		// already.
		$this->plugin_file = 'wordpress-slideshow/wordpress-slideshow.php';

		// Don't forget to call the parent's setUp(), or the plugin won't get installed.
		parent::setUp();
	}

	/**
	 * Test installation and uninstallation.
	 */
	public function test_plugin_activation()
	{

		global $wpdb;

		/*
		 * First test that the plugin is not installed itself properly.
		 */
		// Check that the table was deleted.
		$this->assertNull(Slideshow::plugin_deactivation());

		// Check that all options with a prefix was deleted.
		$this->assertFalse( get_option( 'rt_db_version' ) );
		Slideshow::plugin_activation();
		// Check that a database table was added.
		$this->assertEquals( $wpdb->prefix . 'gallery','wptests_gallery' );

		// Check that an option was added to the database.
		//$this->assertEquals( '1.0.0', get_option( 'rt_db_version' ) );
	}

	public function test_plugin_dectivation()
    {

		 $this->assertNull(Slideshow::plugin_deactivation());
		// Check that an option was added to the database.
		 $this->assertFalse( get_option( 'rt_db_version' ) );

	}
	public function teardown(){
		global $wpdb;
		$table_name = $wpdb->prefix . "gallery";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name ) {
						$wpdb->query("DROP TABLE IF EXISTS $table_name");
		}
		delete_option('rt_db_version');
		//$this->assertEquals( '1.0.0', get_option( 'rt_db_version' ) );
		$test_delete_option = delete_option('rt_db_version');
		$this->assertFalse($test_delete_option);
	}

}
