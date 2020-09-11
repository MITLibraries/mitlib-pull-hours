<?php
/**
 * Class that defines a plugin settings dashboard.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace Mitlib;

/**
 * Defines base widget
 */
class Pull_Hours_Dashboard {

	/**
	 * The id of this widget.
	 */
	const WID = 'pull_hours_dashboard';

	/**
	 * The required permission to access this page.
	 */
	const PERMS = 'manage_options';

	/**
	 * Hook to wp_dashboard_setup to add the widget.
	 */
	public static function init() {
		// Define the dashboard widget.
		if ( current_user_can( self::PERMS ) ) {
			add_options_page(
				'Library hours information',
				'Library hours',
				self::PERMS,
				'mitlib-hours-dashboard',
				array( 'Mitlib\Pull_Hours_Dashboard', 'dashboard' )
			);
		}
	}

	/**
	 * Load the widget code
	 */
	public static function dashboard() {

		// Check user capabilities.
		if ( ! current_user_can( self::PERMS ) ) {
			return;
		}

		// If values have been posted, then we save them.
		$action = filter_input( INPUT_POST, 'action' );
		if ( ! empty( $action ) ) {

			self::update();

		}

		// Otherwise, we render the dashboard page.
		require_once( plugin_dir_path( __FILE__ ) . '../templates/dashboard.php' );
	}

	/**
	 * Update settings based on post inforamtion.
	 */
	public static function update() {
		// Check the nonce.
		check_admin_referer( 'custom_nonce_action', 'custom_nonce_field' );

		// Perform the updates.
		if ( 'update' == filter_input( INPUT_POST, 'action' ) ) {
			// Set default values.
			$google_api_key = '';
			$spreadsheet_key = '';

			// Read submitted values.
			if ( filter_input( INPUT_POST, 'google_api_key' ) ) {
				$google_api_key = sanitize_text_field(
					wp_unslash( filter_input( INPUT_POST, 'google_api_key' ) )
				);
			}

			// Read submitted values.
			if ( filter_input( INPUT_POST, 'spreadsheet_key' ) ) {
				$spreadsheet_key = sanitize_text_field(
					wp_unslash( filter_input( INPUT_POST, 'spreadsheet_key' ) )
				);
			}

			update_option( 'google_api_key', $google_api_key );
			update_option( 'spreadsheet_key', $spreadsheet_key );

			// Perform the harvesting.
			$harvester = new Pull_Hours_Harvester();
			$harvester->harvest();

		}

		// Add the success message.
		echo( '<div class="updated"><p>The library hours settings have been updated. Please <a href="/hours">check the public hours page</a> to verify success.</p></div>' );
	}
}
