<?php
/**
 * The template for the hours display widget.
 *
 * @package MITlib Pull Hours
 * @since 0.2.0
 */

?>

<p>Today's hours:
	<span 
		style="display:inline-block; margin-bottom: 0;"
		data-location-hours="<?php echo esc_attr( $instance['location_slug'] ); ?>"></span><br />
	<a href="/hours">See all hours</a>
</p>
