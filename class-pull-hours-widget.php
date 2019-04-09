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

		// Define the dashboard widget.
		if ( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget(
				self::WID, // A unique slug/ID.
				'Library hours information', // Visible name for the widget.
				array( 'mitlib\Pull_Hours_Widget', 'widget' )  // Callback for the main widget content.
			);
		}
	}

	/**
	 * Load the widget code
	 */
	public static function widget() {

		// Use the template to render widget output.
		require_once( 'templates/widget.php' );
	}
}
