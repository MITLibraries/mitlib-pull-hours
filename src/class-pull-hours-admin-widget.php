<?php
/**
 * Class that defines a dashboard widget.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace Mitlib;

/**
 * Defines base widget
 */
class Pull_Hours_Admin_Widget {

	/**
	 * The id of this widget.
	 */
	const WID = 'pull_hours';

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
			wp_add_dashboard_widget(
				self::WID, // A unique slug/ID.
				'Library hours information', // Visible name for the widget.
				array( 'Mitlib\Pull_Hours_Admin_Widget', 'widget' )  // Callback for the main widget content.
			);
		}
	}

	/**
	 * Load the widget code
	 */
	public static function widget() {

		// Check user capabilities.
		if ( ! current_user_can( self::PERMS ) ) {
			return;
		}

		// If values have been posted, then we save them.
		$action = filter_input( INPUT_POST, 'action' );
		if ( ! empty( $action ) ) {

			// Check the nonce.
			check_admin_referer( 'harvester_nonce_action', 'harvester_nonce_field' );

			$cache_timestamp = time();
			update_option( 'cache_timestamp', $cache_timestamp );

			echo( '<div class="updated"><p>Harvester activated...</p></div>' );

		}

		// Use the template to render widget output.
		require_once( plugin_dir_path( __FILE__ ) . '../templates/admin-widget.php' );
	}
}
