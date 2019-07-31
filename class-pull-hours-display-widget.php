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

		$this->form_textfield(
			'widget_title',
			'Widget Title:',
			$widget_title
		);
		$this->form_textfield(
			'location_slug',
			'Location Name:',
			$location_slug,
			'This value should correspond to the name of a location in the Hours spreadsheet.'
		);
	}

	/**
	 * Common template for text fields in widget settings forms.
	 *
	 * @param string   $fieldname The name of the field.
	 * @param string   $fieldtitle The displayed title of the field.
	 * @param variable $fieldvalue The current value of the field.
	 * @param string   $fieldexplain Optional: a statement explaining the purpose of the field.
	 */
	public function form_textfield( $fieldname, $fieldtitle, $fieldvalue, $fieldexplain = null ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $fieldname ) ); ?>">
				<?php echo esc_attr( $fieldtitle ); ?>
				<input
					class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( $fieldname ) ); ?>"
					type="text"
					name="<?php echo esc_attr( $this->get_field_name( $fieldname ) ); ?>"
					value="<?php echo esc_html( $fieldvalue ); ?>">
			</label>
			<?php
			if ( $fieldexplain ) {
				echo esc_html( $fieldexplain );
			}
			?>
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
		// Define expected markup for widget and title containers.
		$allowed = array(
			'aside' => array(
				'class' => array(),
				'id' => array(),
				'role' => array(),
			),
			'div' => array(
				'class' => array(),
				'id' => array(),
				'role' => array(),
			),
			'h2' => array(
				'class' => array(),
			),
			'h3' => array(
				'class' => array(),
			),
		);

		// Use the template to render widget output.
		require_once( 'templates/display-widget.php' );
	}
}
