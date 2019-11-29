<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

require_once( ABSPATH.WPINC.'/widgets/class-wp-widget-calendar.php' );

class WP_Widget_Persian_Calendar extends WP_Widget_Calendar {

	protected static $instance = 0;

	// almost exact copy of core 5.4-alpha-46786
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div id="calendar_wrap" class="calendar_wrap">';
		} else {
			echo '<div class="calendar_wrap">';
		}
		gPersianDateCalendar::get();
		echo '</div>';
		echo $args['after_widget'];

		self::$instance++;
	}
}
