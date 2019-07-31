<?php
/**
 * The template for the display widget for location hours.
 *
 * @package MITlib Pull Hours
 * @since 0.2.0
 */

?>

<?php echo wp_kses( $args['before_widget'], $allowed ); ?>
<?php
if ( $instance['widget_title'] ) {
	echo wp_kses( $args['before_title'], $allowed ) . esc_html( $instance['widget_title'] ) . wp_kses( $args['after_title'], $allowed );
}
?>
<p class="hours-today">Today's hours:
	<span style="display:inline-block;" data-location-hours="<?php esc_attr( $instance['location_slug'] ); ?>"></span><br />
	<a href="/hours">See all hours</a>
</p>
<?php echo wp_kses( $args['after_widget'], $allowed ); ?>
