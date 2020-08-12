<?php
/**
 * Class that defines plugin settings.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace mitlib;

/**
 * Defines base widget
 */
class Pull_Hours_Settings {

	/**
	 * Register the various settings
	 */
	public static function init() {
		// Register the settings used.
		register_setting( 'mitlib_pull_hours', 'cache_timestamp' );
		register_setting( 'mitlib_pull_hours', 'spreadsheet_key' );

		add_settings_section(
			'mitlib_pull_hours_general',
			'General settings',
			array( 'mitlib\Pull_Hours_Settings', 'general' ),
			'mitlib-hours-dashboard'
		);

		add_settings_field(
			'spreadsheet_key',
			'Hours spreadsheet key',
			array( 'mitlib\Pull_Hours_Settings', 'spreadsheet_callback' ),
			'mitlib-hours-dashboard',
			'mitlib_pull_hours_general',
			array(
				'label_for' => 'spreadsheet_key',
				'class' => 'mitlib_hours_row',
			)
		);

		add_settings_field(
			'cache_timestamp',
			'Last harvested',
			array( 'mitlib\Pull_Hours_Settings', 'timestamp_callback' ),
			'mitlib-hours-dashboard',
			'mitlib_pull_hours_general',
			array(
				'label_for' => 'cache_timestamp',
				'class' => 'mitlib_hours_row',
			)
		);
	}

	/**
	 * This is the general description at the top of the settings form.
	 */
	public static function general() {
		echo '';
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function spreadsheet_callback() {
		$spreadsheet_key = get_option( 'spreadsheet_key' );
		$template = file_get_contents( dirname( __FILE__ ) . '/templates/forms/spreadsheet-key.html' );
		// The $allowed_html array needs to be kept updated with the markup
		// used in the spreadsheet-key.html template.
		$allowed_html = array(
			'input' => array(
				'type' => array(),
				'name' => array(),
				'value' => array(),
				'id' => array(),
				'size' => array()
			),
			'p' => array(),
			'br' => array()
		);
		echo wp_kses( sprintf( $template, 'spreadsheet_key', $spreadsheet_key ), $allowed_html );
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function timestamp_callback() {
		$cache_timestamp = get_option( 'cache_timestamp' );
		$last_updated = new DateTime( gmdate( 'M j, Y g:i:s A T', $cache_timestamp ) );
		$last_updated->setTimezone( new DateTimeZone( 'America/New_York' ) );
		echo esc_html( $last_updated->format( 'M j, Y g:i:s A T' ) );
	}
}
