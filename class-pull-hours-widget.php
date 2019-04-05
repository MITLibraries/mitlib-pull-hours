<?php
/**
 * Class that defines a dashboard widget.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace mitlib;

/**
 * Defines base widget
 */
class Pull_Hours_Widget {

	/**
	 * The id of this widget.
	 */
	const WID = 'pull_hours';

	/**
	 * Hook to wp_dashboard_setup to add the widget.
	 */
	public static function init() {
		// Register the settings used.
		Pull_Hours_Widget::register_settings();

		// Define settings page.
		if ( current_user_can( 'manage_options' ) ) {
			echo( 'Calling settings_page...' );
			add_action(
				'admin_menu',
				'mitlib\Pull_Hours_Widget::settings_page'
			);
		}

		// Define the dashboard widget.
		if ( current_user_can( 'activate_plugins' ) ) {
			wp_add_dashboard_widget(
				self::WID, // A unique slug/ID.
				'Library hours information', // Visible name for the widget.
				array( 'mitlib\Pull_Hours_Widget', 'widget' )  // Callback for the main widget content.
			);
		}
	}

	/**
	 * Register widget settings
	 */
	public static function register_settings() {
		$args = array(
			'type' => 'string',
			'default' => NULL,
		);
		register_setting( 'mitlib_pull_hours', 'spreadsheet_key', $args );
	}

	/**
	 * Define settings page under Settings.
	 */
	public static function settings_page() {
		echo( '...Settings_page called' );
		add_dashboard_page(
			'Library hours integration',
			'Library hours',
			'manage_options',
			'mitlib-pull-hours',
			array( 'mitlib\Pull_Hours_Widget', 'settings_page_html' )
		);
		add_submenu_page(
			'options-general.php',
			'Library hours integration',
			'Library hours',
			'manage_options',
			'mitlib-pull-hours',
			array( 'mitlib\Pull_Hours_Widget', 'settings_page_html' )
		);
	}

	/**
	 * Define settings page markup.
	 */
	public static function settings_page_html() {
		require_once( 'templates/settings.php' );
	}

	/**
	 * Load the widget code
	 */
	public static function widget() {
		// Load settings
		$spreadsheet_key = get_option( 'spreadsheet_key' );

		// Use the template to render widget output.
		require_once( 'templates/widget.php' );
	}
}
