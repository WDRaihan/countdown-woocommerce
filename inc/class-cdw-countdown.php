<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit();
}

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    CDW_COUNTDOWN
 */
class CDW_COUNTDOWN {

	/**
	 * Define the core functionality
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->load_dependencies();
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

	}

	/**
	 * Load the required dependencies
	 *
	 * Include the following files that make up the plugin:
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/admin/class-cdw-countdown-metabox.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/admin/class-cdw-countdown-settings.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/class-cdw-countdown-functions.php';

	}
	
	/**
	 * Enqueue scripts in frontend
	 *
	 * @since    1.0.0
	 */
	function enqueue_scripts() {
		if ( function_exists( 'is_product' ) ) {
			// Check if we are on a single product page
			if ( is_product() ) {
				$product_id = get_the_ID();
				$enable_countdown = !empty(get_post_meta($product_id, 'cdw_enable_countdown', true)) ? get_post_meta($product_id, 'cdw_enable_countdown', true) : '';
				if($enable_countdown == '1'){
					wp_enqueue_script( 'cdw_script', CDW_DIR_URL . 'assets/js/countdown.js', array('jquery'), CDW_VERSION, true );
				}
			}
		}
	}
	
	/**
	 * Enqueue admin scripts
	 *
	 * @since    1.0.0
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'cdw_admin_style', CDW_DIR_URL . 'assets/css/admin-styles.css' );
		
		wp_enqueue_script( 'cdw_admin_script', CDW_DIR_URL . 'assets/js/admin-scripts.js', array('jquery'), CDW_VERSION, true );
	}


}





