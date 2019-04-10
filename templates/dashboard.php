<?php
/**
 * The template for the pull hours widget.
 *
 * @package MITlib Pull Hours
 * @since 0.0.2
 */

?>

<div class="wrap">
	<form method="post" action="">
		<?php
			wp_nonce_field( 'custom_nonce_action', 'custom_nonce_field' );
			settings_fields( 'mitlib_pull_hours' );
			do_settings_sections( 'mitlib-hours-dashboard' );
			submit_button( 'Save settings' );
		?>
	</form>
</div>