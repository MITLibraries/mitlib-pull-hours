<?php
/**
 * Class that defines a quick test admin widget.
 *
 * @package MITlib Pull Hours
 * @since 0.4.0
 */

namespace Mitlib\Pullhours;

/**
 * Implements an administrative widget.
 */
class TestAdminWidget {

	const PERMS = 'manage_options';

	const WID = 'testadminwidget';

	public function init() {
		if ( ! current_user_can( self::PERMS ) ) {
			error_log( 'Test Admin Widget fails init on permission check' );
			return;
		}

		wp_add_dashboard_widget(
			self::WID, // A unique slug/ID
			'Test Admin Widget', // Visible name for the widget.
			array( 'Mitlib\Pullhours\TestAdminWidget', 'widget' ) // Callback for widget content.
		);
	}

	public function widget() {
		if ( ! current_user_can( self::PERMS ) ) {
			return;
		}

		require( plugin_dir_path( __FILE__ ) . '../templates/test-admin-widget.php' );
	}
}
