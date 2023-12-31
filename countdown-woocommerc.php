<?php
/**
 * Plugin Name: Countdown WooCommerce
 * Plugin URI: 
 * Description: 
 * Version: 1.0.0
 * Author: 
 * Author URI: 
 * License: GPL-2.0+
 * License URI: 
 * Text Domain: countdown_woocommerce
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Currently plugin version.
 */
define( 'CDW_VERSION', '1.0.0' );

/**
 * Define plugin directory url
 */
define( 'CDW_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Define Text Domain
 */
define( 'CDW_TEXTDOMAIN', 'countdown_woocommerce' );

/**
 * The core class
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-cdw-countdown.php';

/**
 * Initialize plugin for localization
 *
 * @uses load_plugin_textdomain()
 */
add_action('init', 'cdw_countdown_localization_setup');
function cdw_countdown_localization_setup(){
	load_plugin_textdomain(CDW_TEXTDOMAIN, false, dirname(__FILE__).'/languages');
}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function cdw_countdown_run() {
	if (!is_plugin_active('woocommerce/woocommerce.php')) {
	
        add_action('admin_notices', 'cdw_countdown_woocommerce_missing_notice');
    }else{
	
		new CDW_COUNTDOWN();
	}
}
cdw_countdown_run();

/**
 * Admin notice
 *
 * @since    1.0.0
 */
function cdw_countdown_woocommerce_missing_notice() {
    ?>
	<div class="notice notice-error is-dismissible">
		<p><?php echo esc_html__('WooCommerce is not installed or activated. Please install and activate WooCommerce to use the Countdown WooCommerce plugin.', CDW_TEXTDOMAIN); ?></p>
	</div>
	<?php
}
