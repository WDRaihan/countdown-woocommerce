<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit();
}
/**
 * Class CDW_Countdown_Functions
 */
class CDW_Countdown_Functions {
	
	/**
	 * Define the core functionality
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		add_action( 'wp', array( $this, 'countdown_init' ));
		//Redirect if expired
		add_action('template_redirect', array( $this, 'redirect_product_after_expired' ));
		//Visible if hide after expired
		add_filter('woocommerce_product_is_visible', array( $this, 'hide_product_after_expired' ), 10, 2);

	}
	
	/**
	 * Initilizing the countdown
	 *
	 * @since    1.0.0
	 */
	public function countdown_init() {
		
		if ( function_exists( 'is_product' ) ) {
			// Check if we are on a single product page
			if ( is_product() ) {
				$product_id = get_the_ID();
				
				$enable_countdown = !empty(get_post_meta($product_id, 'cdw_enable_countdown', true)) ? get_post_meta($product_id, 'cdw_enable_countdown', true) : '';
				
				if( $enable_countdown != '1' ) return;
				
				$countdown_position = !empty(get_post_meta($product_id, 'cdw_countdown_position', true)) ? get_post_meta($product_id, 'cdw_countdown_position', true) : 'Before-Add-to-Cart';
				
				if( $countdown_position == 'Before-Add-to-Cart' ){
					add_action('woocommerce_before_add_to_cart_form', array( $this, 'display_countdown_based_on_countdown_position' ), 10);
					
				}elseif( $countdown_position == 'After-Add-to-Cart' ){
					add_action('woocommerce_after_add_to_cart_form', array( $this, 'display_countdown_based_on_countdown_position' ), 10);
					
				}elseif( $countdown_position == 'Before-Title' ){
					add_action('woocommerce_single_product_summary', array( $this, 'display_countdown_based_on_countdown_position' ), 1);
					
				}elseif( $countdown_position == 'After-Title' ){
					add_action('woocommerce_single_product_summary', array( $this, 'display_countdown_based_on_countdown_position' ), 6);
					
				}elseif( $countdown_position == 'Before-Product' ){
					add_action('woocommerce_before_single_product', array( $this, 'display_countdown_based_on_countdown_position' ), 99);
					
				}
			}
		}
	}
	
	/**
	 * Display the countdown based on position
	 *
	 * @since    1.0.0
	 */
	public function display_countdown_based_on_countdown_position() {
		$product_id = get_the_ID();
		$end_time = !empty(get_post_meta($product_id, 'cdw_end_time', true)) ? get_post_meta($product_id, 'cdw_end_time', true) : '1985-01-30T12:01:59';
		$action_after_expired = !empty(get_post_meta($product_id, 'cdw_action_after_expired', true)) ? get_post_meta($product_id, 'cdw_action_after_expired', true) : 'Show-Message';
		$expired_message = !empty(get_post_meta($product_id, 'cdw_expired_message', true)) ? get_post_meta($product_id, 'cdw_expired_message', true) : 'Expired';
		$countdown_message = !empty(get_post_meta($product_id, 'cdw_countdown_message', true)) ? get_post_meta($product_id, 'cdw_countdown_message', true) : '';

		if( !$this->is_expired($product_id) ) {
			
			echo '<div class="cdw-countdown">';
			if(!empty($countdown_message)){
				echo '<p>'.esc_html($countdown_message).'</p>';
			}
			echo '<div id="cdw_countdown" class="cdw-countdown" data-end-time="'.esc_attr($end_time).'"></div>';
			echo '</div>';
			
		}else{
			
			if($action_after_expired == 'Show-Message'){
				echo '<div class="cdw-countdown">'.esc_html($expired_message).'</div>';
			}
		}
	}
	
	/**
	 * Redirect from product page if countdown expired
	 *
	 * @since    1.0.0
	 */
	public function redirect_product_after_expired() {
		// Get the current product ID
		$product_id = get_the_ID();

		$enable_countdown = !empty(get_post_meta($product_id, 'cdw_enable_countdown', true)) ? get_post_meta($product_id, 'cdw_enable_countdown', true) : '';
		$action_after_expired = !empty(get_post_meta($product_id, 'cdw_action_after_expired', true)) ? get_post_meta($product_id, 'cdw_action_after_expired', true) : 'Show-Message';
		
		$redirect_url = !empty(get_post_meta($product_id, 'cdw_redirect_url', true)) ? get_post_meta($product_id, 'cdw_redirect_url', true) : home_url();

		if(is_product()){
			if( $enable_countdown == '1' && $this->is_expired($product_id) && $action_after_expired == 'Redirect' ){

				// Perform the redirect to the home page
				wp_redirect(esc_url($redirect_url));
				exit;
			}

			//Redirect after expired if hidden
			if ( $enable_countdown == '1' && $this->is_expired($product_id) && $action_after_expired == 'Hide' ) {

				wp_redirect($redirect_url);
				exit;
			}

		}
	}
	
	/**
	 * Hide the product if countdown expired
	 *
	 * @since    1.0.0
	 */
	function hide_product_after_expired($visible, $product_id) {
		// Check if the product is visible by default
		if ($visible) {
			// Retrieve the meta fields for the product
			$enable_countdown = !empty(get_post_meta($product_id, 'cdw_enable_countdown', true)) ? get_post_meta($product_id, 'cdw_enable_countdown', true) : '';
			$action_after_expired = !empty(get_post_meta($product_id, 'cdw_action_after_expired', true)) ? get_post_meta($product_id, 'cdw_action_after_expired', true) : 'Show-Message';

			if ( $enable_countdown == '1' && $this->is_expired($product_id) && $action_after_expired == 'Hide' ) {

				$visible = false; // Hide the product
			}
		}

		return $visible;
	}
	
	/**
	 * Check the countdown if expired or not
	 *
	 * Return: true or false
	 *
	 * @since    1.0.0
	 */
	private function is_expired($product_id){
		
		$end_time = !empty(get_post_meta($product_id, 'cdw_end_time', true)) ? get_post_meta($product_id, 'cdw_end_time', true) : '1985-01-30T12:01:59';

		$timezone = apply_filters( 'cdw_timezone', null );

		$current_time = wp_date("Y-m-d\TH:i:s", null, $timezone);

		if($current_time == $end_time || $current_time > $end_time){
			//Expired
			return true;
		}else{
			return false;
		}

	}
	
}

new CDW_Countdown_Functions();