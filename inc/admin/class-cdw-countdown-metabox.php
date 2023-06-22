<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit();
}

/**
 * The CDW_Countdown_Metabox class.
 *
 * @since             1.0.0
 */
class CDW_Countdown_Metabox {

	/**
	 * Array that defines display locations.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $display_locations The list of locations where this meta box should be displayed.
	 */
	private $display_locations = [
		'product',
	];
	
	/**
	 * Variables array that defines fields/options for the meta box.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $fields The list of user defined fields/options.
	 */
	private $fields = [
		'cdw_enable_countdown' => [
			'type' => 'checkbox',
			'label' => 'Enable Countdown',
			'default' => '',
		],
		'cdw_end_time' => [
			'type' => 'datetime-local',
			'label' => 'End Time',
			'default' => '',
		],
		'cdw_countdown_message' => [
			'type' => 'text',
			'label' => 'Countdown Message',
			'default' => '',
		],
		'cdw_action_after_expired' => [
			'type' => 'select',
			'label' => 'Action After Expired',
			'default' => '',
			'options' => [
				'Redirect',
				'Hide',
				'Show-Message',
			],
		],
		'cdw_redirect_url' => [
			'type' => 'url',
			'label' => 'Redirect URL',
			'default' => '',
		],
		'cdw_countdown_position' => [
			'type' => 'select',
			'label' => 'Countdown Position',
			'default' => '',
			'options' => [
				'Before-Product',
				'Before-Title',
				'After-Title',
				'Before-Add-to-Cart',
				'After-Add-to-Cart',
			],
		],
		'cdw_expired_message' => [
			'type' => 'text',
			'label' => 'Expired Message',
			'default' => 'Expired',
		],
	];
	
	/**
	 * Custom_Meta_Box constructor.
	 *
	 * Adds actions to WordPress hooks "add_meta_boxes" and "save_post".
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_meta_box_fields' ] );
	}
	
	/**
	 * Adds meta boxes to appropriate WordPress screens.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_meta_boxes() : void {
		foreach ( $this->display_locations as $location ) {
			add_meta_box(
				'cdw_countdown_metabox', /* The id of our meta box. */
				'Countdown Settings', /* The title of our meta box. */
				[ $this, 'render_meta_box_fields' ], /* The callback function that renders the metabox. */
				$location, /* The screen on which to show the box. */
				'normal', /* The placement of our meta box. */
				'high', /* The priority of our meta box. */
			);
		}
	}
	
	/**
	 * Renders the Meta Box and its fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function render_meta_box_fields(WP_Post $post) : void {
		wp_nonce_field( 'cdw_countdown_metabox_data', 'cdw_countdown_metabox_nonce' );
		echo '<h3>'.esc_html__('Set up a countdown for this product.',CDW_TEXTDOMAIN).'</h3>';
		$html = '';
		foreach( $this->fields as $field_id => $field ){
			$meta_value = get_post_meta( $post->ID, $field_id, true );
			if ( empty( $meta_value ) && isset( $field['default'] ) ) {
				$meta_value = $field['default'];
			}
	
			$field_html = $this->render_input_field( $field_id, $field, $meta_value );
			$label = "<label for='$field_id'>{$field['label']}</label>";
			$html .= $this->format_field( $label, $field_html );
		}
		echo '<table class="form-table"><tbody>' . $html . '</tbody></table>';
	}
	
	/**
	 * Formats each field to table display.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $label The field label.
	 * @param string $field The field HTML code.
	 *
	 * @return string
	 */
	public function format_field( string $label, string $field ): string {
		return '<tr class="form-field"><th>' . $label . '</th><td>' . $field . '</td></tr>';
	}
	
	/**
	 * Renders each individual field HTML code.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field_id The field ID.
	 * @param array $field The field configuration array.
	 * @param string $field_value The field value.
	 *
	 * @return string The HTML code.
	 */
	public function render_input_field( string $field_id, array $field, string $field_value): string {
		switch( $field['type'] ){
			case 'select': {
				$field_html = '<select name="'.esc_attr($field_id).'" id="'.esc_attr($field_id).'">';
					foreach( $field['options'] as $key => $value ) {
						$key = !is_numeric( $key ) ? $key : $value;
						$selected = '';
						if( $field_value === $key ) {
							$selected = 'selected="selected"';
						}
						$field_html .= '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
					}
				$field_html .= '</select>';
				break;
			}
			case 'textarea': {
				$field_html = '<textarea name="' . esc_attr($field_id) . '" id="' . esc_attr($field_id) . '" rows="6">' . esc_html($field_value) . '</textarea>';
				break;
			}
			case 'checkbox': {
				$checked = '';
				if( $field_value == '1' ) {
					$checked = 'checked="checked"';
				}
				$field_html = "<input type='".esc_attr($field['type'])."' id='".esc_attr($field_id)."' name='".esc_attr($field_id)."' value='1' $checked />";
				break;
			}
			case 'datetime-local': {
				$field_html = "<input type='".esc_attr($field['type'])."' id='".esc_attr($field_id)."' name='".esc_attr($field_id)."' value='".esc_attr(date('Y-m-d\TH:i:s', strtotime($field_value)))."' />";
				break;
			}
			case 'url': {
				$field_html = "<input type='".esc_attr($field['type'])."' id='".esc_attr($field_id)."' name='".esc_attr($field_id)."' value='".esc_url($field_value)."' />";
				break;
			}
			default: {
				$field_html = "<input type='".esc_attr($field['type'])."' id='".esc_attr($field_id)."' name='".esc_attr($field_id)."' value='".esc_html($field_value)."' />";
				break;
			}
		}
	
		return $field_html;
	}
	
	/**
	 * Called when this metabox is saved.
	 *
	 * Saves the new meta values of our metabox.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return int The post ID.
	 */
	public function save_meta_box_fields( int $post_id ) {
		if ( ! isset( $_POST['cdw_countdown_metabox_nonce'] ) ) return;
	
		$nonce = $_POST['cdw_countdown_metabox_nonce'];
		if ( !wp_verify_nonce( $nonce, 'cdw_countdown_metabox_data' ) ) return;
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
		foreach ( $this->fields as $field_id => $field ) {
			if( isset( $_POST[$field_id] ) ){
				// Sanitize fields that need to be sanitized.
				switch( $field['type'] ) {
					case 'email':
						$_POST[$field_id] = sanitize_email( $_POST[$field_id] );
						break;
					case 'url':
						$_POST[$field_id] = sanitize_url( $_POST[$field_id] );
						break;
					default:
						$_POST[$field_id] = sanitize_text_field( $_POST[$field_id] );
						break;
				}
				update_post_meta( $post_id, $field_id, $_POST[$field_id] );
			}else{
				switch( $field['type'] ) {
					case 'checkbox':
						$_POST[$field_id] = '0';
						break;
				}
				update_post_meta( $post_id, $field_id, $_POST[$field_id] );
			}
		}
	}
	
}

new CDW_Countdown_Metabox();
