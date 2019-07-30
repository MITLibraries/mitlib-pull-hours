<?php
/**
 * Class that defines a public-facing widget that displays hours information.
 *
 * @package MITlib Pull Hours
 * @since 0.2.0
 */

namespace mitlib;

/**
 * Defines a public-facing widget for displaying hours information
 */
class Pull_Hours_Display_Widget extends \WP_Widget {

	/**
	 * Overridden constructor from WP_Widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'pull-hours-display-widget',
			'description' => __( 'Hours widget for one location', 'hoursdisplay' ),
		);
		parent::__construct(
			'hoursdisplay',
			__( 'Location Hours', 'hoursdisplay' ),
			$widget_ops
		);
	}

	/**
	 * Widget instance form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$widget_title = $instance['widget_title'];
		$location_slug = $instance['location_slug'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>">
				<?php esc_attr_e( 'Widget Title:' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>"
				value="<?php echo esc_html( $widget_title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'location_slug' ) ); ?>">
				<?php esc_attr_e( 'Location Name:' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'location_slug' ) ); ?>"
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'location_slug' ) ); ?>"
				value="<?php echo esc_html( $location_slug ); ?>">
			This value should correspond to the name of a location in the Hours spreadsheet.
		</p>
		<?php
	}

	/**
	 * Registers widget.
	 */
	public static function init() {
		register_widget( 'mitlib\Pull_Hours_Display_Widget' );
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widget_title'] = $new_instance['widget_title'];
		$instance['location_slug'] = $new_instance['location_slug'];
		return $instance;
	}

	/**
	 * Widget() builds the output
	 *
	 * @param array $args See WP_Widget in Developer documentation.
	 * @param array $instance See WP_Widget in Developer documentation.
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/
	 */
	public function widget( $args, $instance ) {
		// Render markup.
		echo $args['before_widget'];
		if ( $instance['widget_title'] ) {
			echo $args['before_title'] . $instance['widget_title'] . $args['after_title'];
		}
		echo '<p class="hours-today">Today\'s Hours:';
		echo '<span data-location-hours="' . $instance['location_slug'] . '"></span>';
		echo ' | <a href="/hours" class="link-hours-all">See all hours</a>';
		echo '</p>';
		echo $args['after_widget'];
	}
}
