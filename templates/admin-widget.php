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

// Convert timestamp to the right timezone.
$cache_timestamp = get_option( 'cache_timestamp' );
$last_updated = new DateTime( gmdate( 'M j, Y g:i:s A T', $cache_timestamp ) );
$last_updated->setTimezone( new DateTimeZone( 'America/New_York' ) );

// Calculate time interval since last harvest.
$now = new DateTime();
$ago = $now->diff( $last_updated, true );
switch ( $ago ) {
	case $ago->y > 0:
		$ago_msg = $ago->y . ' years ago';
		break;
	case $ago->m > 0:
		$ago_msg = $ago->m . ' months ago';
		break;
	case $ago->d > 0:
		$ago_msg = $ago->d . ' days ago';
		break;
	case $ago->h > 0:
		$ago_msg = $ago->h . ' hours ago';
		break;
	case $ago->i > 0:
		$ago_msg = $ago->i . ' minutes ago';
		break;
	case $ago->s > 0:
		$ago_msg = $ago->s . ' seconds ago';
		break;
	default:
		$ago_msg = 'less than a second ago';
}
?>
<p>Hours spreadsheet key:<br />
	<a href="<?php echo esc_url( $spreadsheet_url ); ?>">
		<?php echo esc_html( $spreadsheet_key ); ?>
	</a>
</p>
<p>Information about library hours was last updated<br /><?php echo esc_html( $ago_msg ); ?><br />
<?php echo esc_html( $last_updated->format( 'M j, Y g:i:s A T' ) ); ?></p>
<p>To update the hours information, visit <a href="options-general.php?page=mitlib-hours-dashboard">the Library hours settings page</a>.</p>
