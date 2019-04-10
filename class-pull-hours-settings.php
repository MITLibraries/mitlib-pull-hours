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
			'Library hours settings',
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
		echo '<p>These settings allow you to customize certain aspects of the MIT Libraries\' hours system.</p>';
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function spreadsheet_callback() {
		$spreadsheet_key = get_option( 'spreadsheet_key' );
		require_once( 'templates/forms/spreadsheet-key.php' );
	}

	/**
	 * Field-rendering callback for the Google Spreadsheet key
	 */
	public static function timestamp_callback() {
		$cache_timestamp = get_option( 'cache_timestamp' );
		require_once( 'templates/forms/cache-timestamp.php' );
	}
}
