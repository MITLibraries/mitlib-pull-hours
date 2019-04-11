<?php
/**
 * The template for the pull hours widget.
 *
 * @package MITlib Pull Hours
 * @since 0.0.2
 */

?>

<div class="wrap">
	<h1>Library hours cache settings</h1>
	<p>This form will update the local cache of library hours based on the Google spreadsheet identified below. For more complete documentation about our practices around library hours, <a href="https://wikis.mit.edu/confluence/display/UXWS/Hours">please consult the UX wiki</a>.</p>
	<form method="post" action="">
		<?php
			wp_nonce_field( 'custom_nonce_action', 'custom_nonce_field' );
			settings_fields( 'mitlib_pull_hours' );
			do_settings_sections( 'mitlib-hours-dashboard' );
			submit_button( 'Update hours cache' );
		?>
	</form>
</div>
