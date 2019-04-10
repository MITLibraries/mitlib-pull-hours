<?php
/**
 * The template for the pull hours widget.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

?>

<?php
	$spreadsheet_key = get_option( 'spreadsheet_key' );
	$spreadsheet_url = 'https://docs.google.com/spreadsheets/d/' . $spreadsheet_key . '/edit';

	$cache_timestamp = get_option( 'cache_timestamp' );
?>
<p>Hours spreadsheet key:<br />
	<a href="<?php echo esc_url( $spreadsheet_url ); ?>">
		<?php echo esc_html( $spreadsheet_key ); ?>
	</a>
</p>
<p>Information about library hours was last updated:<br />
<?php echo esc_html( date( 'M j, Y g:i:s A T', $cache_timestamp ) ); ?></p>
