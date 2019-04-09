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
				array( 'mitlib\Pull_Hours_Dashboard', 'dashboard' )
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
		if ( ! empty( filter_input( INPUT_POST, 'action' ) ) ) {

			self::update();

		}

		// Otherwise, we render the dashboard page.
		require_once( 'templates/dashboard.php' );
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
			$spreadsheet_key = '';

			// Read submitted values.
			if ( filter_input( INPUT_POST, 'spreadsheet_key' ) ) {
				$spreadsheet_key = sanitize_text_field(
					wp_unslash( filter_input( INPUT_POST, 'spreadsheet_key' ) )
				);
			}

			update_option( 'spreadsheet_key', $spreadsheet_key );
		}

		// Add the success message.
		echo( '<div class="updated"><p>The library hours settings have been updated.</p></div>' );
	}
}
