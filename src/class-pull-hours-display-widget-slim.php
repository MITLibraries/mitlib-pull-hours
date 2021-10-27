<?php
/**
 * Class that defines a very slim public-facing widget that displays hours information.
 *
 * @package MITlib Pull Hours
 * @since 0.4.0
 */

namespace Mitlib;

/**
 * Defines a public-facing widget for displaying hours information
 */
class Pull_Hours_Display_Widget_Slim extends \WP_Widget {

	/**
	 * Overridden constructor from WP_Widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'pull-hours-display-widget-slim',
			'description' => __( 'Slim hours widget for one location', 'hoursdisplayslim' ),
		);
		parent::__construct(
			'hoursdisplayslim',
			__( 'Location Hours - Slim', 'hoursdisplayslim' ),
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
		$title = $instance['title'];
		$location_label = $instance['location_label'];

		$this->form_textfield(
			'title',
			'Location Name:',
			$title,
			'This value should correspond to the name of a location in the Hours spreadsheet.'
		);
		$this->form_textfield(
			'location_label',
			'Location Label:',
			$location_label,
			'This will be displayed just before the hours information. "Today\'s hours:" is a good default value.'
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
		register_widget( 'Mitlib\Pull_Hours_Display_Widget_Slim' );
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
		$instance['title'] = $new_instance['title']; // Use the location slug as the title for easy identification.
		$instance['location_label'] = $new_instance['location_label'];
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
		$allowed = $this->widget_allowed();

		// Render markup.
		echo wp_kses( $args['before_widget'], $allowed );
		$template = file_get_contents( dirname( __FILE__ ) . '/../templates/display-widget-slim.html' );
		echo wp_kses( sprintf( $template, $instance['location_label'], $instance['title'] ), $allowed );
		echo wp_kses( $args['after_widget'], $allowed );
	}

	/**
	 * This returns an array of expected tags and attributes for widget
	 * rendering.
	 */
	public function widget_allowed() {
		return array(
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
			'span' => array(
			),
			'strong' => array(
				'data-location-hours' => array()
			)
		);
	}
}
