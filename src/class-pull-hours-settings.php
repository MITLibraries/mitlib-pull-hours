<?php
/**
 * Class that defines plugin settings.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace Mitlib;

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
		register_setting( 'mitlib_pull_hours', 'google_api_key' );
		register_setting( 'mitlib_pull_hours', 'spreadsheet_key' );

		add_settings_section(
			'mitlib_pull_hours_general',
			'General settings',
			array( 'Mitlib\Pull_Hours_Settings', 'general' ),
			'mitlib-hours-dashboard'
		);

		add_settings_field(
			'spreadsheet_key',
			'Hours spreadsheet key',
			array( 'Mitlib\Pull_Hours_Settings', 'spreadsheet_callback' ),
			'mitlib-hours-dashboard',
			'mitlib_pull_hours_general',
			array(
				'label_for' => 'spreadsheet_key',
				'class' => 'mitlib_hours_row',
			)
		);

		add_settings_field(
			'google_api_key',
			'Google Sheets API key',
			array( 'Mitlib\Pull_Hours_Settings', 'google_api_key_callback' ),
			'mitlib-hours-dashboard',
			'mitlib_pull_hours_general',
			array(
				'label_for' => 'google_api_key',
				'class' => 'mitlib_hours_row',
			)
		);

		add_settings_field(
			'cache_timestamp',
			'Last harvested',
			array( 'Mitlib\Pull_Hours_Settings', 'timestamp_callback' ),
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
	public static function google_api_key_callback() {
		$google_api_key = get_option( 'google_api_key' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/forms/google-api-key.php' );
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function spreadsheet_callback() {
		$spreadsheet_key = get_option( 'spreadsheet_key' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/forms/spreadsheet-key.php' );
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function timestamp_callback() {
		$cache_timestamp = get_option( 'cache_timestamp' );
		require_once( plugin_dir_path( __FILE__ ) . '../templates/forms/cache-timestamp.php' );
	}
}
