<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CDW_Countdown_Settings
 *
 * Configure the settings page.
 */
class CDW_Countdown_Settings {

	/**
	 * Capability required by the user to access the My Plugin menu entry.
	 *
	 * @var string $capability
	 */
	private $capability = 'manage_options';

	/**
	 * Array of fields that should be displayed in the settings page.
	 *
	 * @var array $fields
	 */
	private $fields = [
		[
			'id' => 'caountdown-background',
			'label' => 'Caountdown Background',
			'description' => 'Select countdown background color',
			'type' => 'color',
			'placeholder' => '',
		],
		[
			'id' => 'cdw-countdown-number-font-size',
			'label' => 'Countdown Number Font Size',
			'description' => 'Enter the coutdown date time number font size. E.g: 40px',
			'type' => 'text',
			'placeholder' => '40px',
		],
		[
			'id' => 'cdw-countdown-text-font-size',
			'label' => 'Countdown Text Font Size',
			'description' => 'Enter the coutdown date time text font size. E.g: 16px',
			'type' => 'text',
			'placeholder' => '16px',
		],
		[
			'id' => 'cdw-countdown-number-font-color',
			'label' => 'Countdown Number Font Color',
			'description' => 'Select the coutdown date time number font color.',
			'type' => 'color',
			'placeholder' => '',
		],
		[
			'id' => 'cdw-countdown-text-font-color',
			'label' => 'Countdown Text Font Color',
			'description' => '',
			'type' => 'color',
			'placeholder' => '',
		],
		[
			'id' => 'cdw-countdown-message-font-size',
			'label' => 'Message Font Size',
			'description' => 'Enter the coutdown message font size. E.g: 15px',
			'type' => 'text',
			'placeholder' => '15px',
		],
		[
			'id' => 'cdw-countdown-message-font-color',
			'label' => 'Message Font Color',
			'description' => 'Select the coutdown message font color.',
			'type' => 'color',
			'placeholder' => '',
		],
	];

	/**
	 * The Plugin Settings constructor.
	 */
	function __construct() {
		add_action( 'admin_init', [$this, 'settings_init'] );
		add_action( 'admin_menu', [$this, 'options_page'] );
	}

	/**
	 * Register the settings and all fields.
	 */
	function settings_init() : void {

		// Register a new setting this page.
		register_setting( 'cdw_countdown_settings', 'cdw_settings_options' );


		// Register a new section.
		add_settings_section(
			'cdw_countdown_settings-section',
			__( '', CDW_TEXTDOMAIN ),
			[$this, 'render_section'],
			'cdw_countdown_settings'
		);


		/* Register All The Fields. */
		foreach( $this->fields as $field ) {
			// Register a new field in the main section.
			add_settings_field(
				$field['id'], /* ID for the field. */
				__( $field['label'], CDW_TEXTDOMAIN ), /* Label for the field. */
				[$this, 'render_field'], /* The name of the callback function. */
				'cdw_countdown_settings', /* The menu page on which to display this field. */
				'cdw_countdown_settings-section', /* The section of the settings page in which to show the box. */
				[
					'label_for' => $field['id'], /* The ID of the field. */
					'class' => 'cdw_settings_row', /* The class of the field. */
					'field' => $field, /* Custom data for the field. */
				]
			);
		}
	}

	/**
	 * Add a subpage to the WordPress Settings menu.
	 */
	function options_page() : void {
		add_menu_page(
			'Settings', /* Page Title */
			'Countdown WooCommerce', /* Menu Title */
			$this->capability, /* Capability */
			'cdw_countdown_settings', /* Menu Slug */
			[$this, 'render_options_page'], /* Callback */
			'dashicons-clock', /* Icon */
			'30', /* Position */
		);
	}

	/**
	 * Render the settings page.
	 */
	function render_options_page() : void {

		// check user capabilities
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'cdw_settings_messages', 'cdw_settings_message', __( 'Settings Saved', 'cdw_countdown_settings' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'cdw_settings_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="description"></h2>
			<form action="options.php" method="post">
				<?php
				/* output security fields for the registered setting "cdw_settings" */
				settings_fields( 'cdw_countdown_settings' );
				/* output setting sections and their fields */
				do_settings_sections( 'cdw_countdown_settings' );
				/* output save settings button */
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Args to configure the field.
	 */
	function render_field( array $args ) : void {

		$field = $args['field'];

		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'cdw_settings_options' );

		switch ( $field['type'] ) {

			case "text": {
				?>
				<input
					type="text"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>" 
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

			case "checkbox": {
				?>
				<input
					type="checkbox"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="1"
					<?php echo isset( $options[ $field['id'] ] ) ? ( checked( $options[ $field['id'] ], 1, false ) ) : ( '' ); ?>
				>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

			case "textarea": {
				?>
				<textarea
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
				><?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?></textarea>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

			case "select": {
				?>
				<select
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
				>
					<?php foreach( $field['options'] as $key => $option ) { ?>
						<option value="<?php echo $key; ?>" 
							<?php echo isset( $options[ $field['id'] ] ) ? ( selected( $options[ $field['id'] ], $key, false ) ) : ( '' ); ?>
						>
							<?php echo $option; ?>
						</option>
					<?php } ?>
				</select>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}
			
			case "url": {
				?>
				<input
					type="url"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

			case "color": {
				?>
				<input
					type="color"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

			case "date": {
				?>
				<input
					type="date"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					name="cdw_settings_options[<?php echo esc_attr( $field['id'] ); ?>]"
					value="<?php echo isset( $options[ $field['id'] ] ) ? esc_attr( $options[ $field['id'] ] ) : ''; ?>"
				>
				<p class="description">
					<?php esc_html_e( $field['description'], CDW_TEXTDOMAIN ); ?>
				</p>
				<?php
				break;
			}

		}
	}


	/**
	 * Render a section on a page, with an ID and a text label.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     An array of parameters for the section.
	 *
	 *     @type string $id The ID of the section.
	 * }
	 */
	function render_section( array $args ) : void {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Countdown Settings', CDW_TEXTDOMAIN ); ?></p>
		<?php
	}

}

new CDW_Countdown_Settings();

