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
?>
<p>Hours spreadsheet key:<br />
	<a href="<?php echo esc_url( $spreadsheet_url ); ?>">
		<?php echo esc_html( $spreadsheet_key ); ?>
	</a>
</p>
<p>Information about library hours was last updated:<br />
April 2, 2019 (two days ago)</p>
