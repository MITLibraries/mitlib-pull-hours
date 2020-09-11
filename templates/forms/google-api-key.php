<?php
/**
 * The field template for the Google Spreadsheet key
 *
 * @package MITlib Pull Hours
 * @since 0.0.2
 */

?>

<input
	type="text"
	name="<?php echo esc_attr( 'google_api_key' ); ?>"
	value="<?php echo esc_attr( $google_api_key ); ?>"
	id="<?php echo esc_attr( 'google_api_key' ); ?>"
	size="60">
<p>Please see EngX or InfraEng if you are not sure what this value should be. The current key should be listed on the Libraries' <a href="https://console.developers.google.com/apis/credentials">Google Developer Console</a>.</p>
