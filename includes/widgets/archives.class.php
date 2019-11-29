<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

require_once( ABSPATH.WPINC.'/widgets/class-wp-widget-archives.php' );

class WP_Widget_Persian_Archives extends WP_Widget_Archives {

	// almost exact copy of core 5.4-alpha-46786
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Archives' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $d ) {
			$dropdown_id = "{$this->id_base}-dropdown-{$this->number}";
			?>
		<label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo $title; ?></label>
		<select id="<?php echo esc_attr( $dropdown_id ); ?>" name="archive-dropdown">
			<?php
			/**
			 * Filters the arguments for the Archives widget drop-down.
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see wp_get_archives()
			 *
			 * @param array $args     An array of Archives widget drop-down arguments.
			 * @param array $instance Settings for the current Archives widget instance.
			 */
			$dropdown_args = apply_filters(
				'widget_archives_dropdown_args',
				array(
					'type'            => 'monthly',
					'format'          => 'option',
					'show_post_count' => $c,
				),
				$instance
			);

			switch ( $dropdown_args['type'] ) {
				case 'yearly':
					$label = __( 'Select Year' );
					break;
				case 'monthly':
					$label = __( 'Select Month' );
					break;
				case 'daily':
					$label = __( 'Select Day' );
					break;
				case 'weekly':
					$label = __( 'Select Week' );
					break;
				default:
					$label = __( 'Select Post' );
					break;
			}

			$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
			?>

			<option value=""><?php echo esc_attr( $label ); ?></option>
			<?php gPersianDateArchives::get( $dropdown_args ); ?>

		</select>

<script<?php echo $type_attr; ?>>
/* <![CDATA[ */
(function() {
	var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
	function onSelectChange() {
		if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
			document.location.href = this.options[ this.selectedIndex ].value;
		}
	}
	dropdown.onchange = onSelectChange;
})();
/* ]]> */
</script>

		<?php } else { ?>
		<ul>
			<?php
			gPersianDateArchives::get(
				/**
				 * Filters the arguments for the Archives widget.
				 *
				 * @since 2.8.0
				 * @since 4.9.0 Added the `$instance` parameter.
				 *
				 * @see wp_get_archives()
				 *
				 * @param array $args     An array of Archives option arguments.
				 * @param array $instance Array of settings for the current widget.
				 */
				apply_filters(
					'widget_archives_args',
					array(
						'type'            => 'monthly',
						'show_post_count' => $c,
					),
					$instance
				)
			);
			?>
		</ul>
			<?php
		}

		echo $args['after_widget'];
	}
}
