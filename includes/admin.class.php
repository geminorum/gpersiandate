<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateAdmin extends gPersianDateModuleCore
{

	protected $options    = [];
	protected $datepicker = TRUE;

	protected function setup_actions()
	{
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );

		add_filter( 'update_footer', [ 'gPersianDateTranslate', 'html' ], 12 );
	}

	public function admin_init()
	{
		$this->options = gPersianDate()->options();

		register_setting( 'general', 'gpersiandate', [
			'sanitize_callback' => [ $this, 'settings_sanitize' ],
			'show_in_rest'      => FALSE,
		] );
	}

	public function current_screen( $screen )
	{
		if ( 'edit' == $screen->base ) {

			if ( self::req( 'persian_start_date' ) || self::req( 'persian_end_date' ) )
				add_filter( 'posts_where', [ $this, 'posts_where_start_end' ] );

			if ( self::req( 'persian_month' ) )
				add_filter( 'posts_where', [ $this, 'posts_where_persian_month' ] );

			add_filter( 'disable_months_dropdown', [ $this, 'disable_months_dropdown' ], 10, 2 );

			if ( ! empty( $this->options['restrict_fromto'] )
				&& ( 'post' == $screen->post_type
				|| post_type_supports( $screen->post_type, 'date-picker' ) ) ) {

				gPersianDatePicker::enqueue();

				add_action( 'restrict_manage_posts', [ $this, 'restrict_manage_posts_start_end' ], 8, 2 );
			}

		} else if ( 'upload' == $screen->base ) {

			if ( self::req( 'persian_month' ) )
				add_filter( 'posts_where', [ $this, 'posts_where_persian_month' ] );

			add_filter( 'disable_months_dropdown', [ $this, 'disable_months_dropdown' ], 10, 2 );

		} else if ( 'options-general' == $screen->base ) {

			$page = 'general';

			add_settings_field( 'adminbar_clock', __( 'Adminbar Clock', GPERSIANDATE_TEXTDOMAIN ), [ $this, 'field_adminbar_clock' ], $page );
			add_settings_field( 'restrict_month', __( 'Month Restrictions', GPERSIANDATE_TEXTDOMAIN ), [ $this, 'field_restrict_month' ], $page );
			add_settings_field( 'restrict_fromto', __( 'Date Restrictions', GPERSIANDATE_TEXTDOMAIN ), [ $this, 'field_restrict_fromto' ], $page );
		}
	}

	public function settings_sanitize( $input )
	{
		return [
			'adminbar_clock'  => empty( $input['adminbar_clock'] ) ? 0 : 1,
			'restrict_month'  => empty( $input['restrict_month'] ) ? 0 : $input['restrict_month'],
			'restrict_fromto' => empty( $input['restrict_fromto'] ) ? 0 : 1,
		];
	}

	public function field_adminbar_clock( $args )
	{
		$field  = 'adminbar_clock';
		$option = empty( $this->options[$field] ) ? 0 : $this->options[$field];

		echo '<select name="gpersiandate['.$field.']" id="gpersiandate-'.$field.'">';
			?><option value="0" <?php selected( $option, 0 ); ?>><?php esc_html_e( 'Disabled', GPERSIANDATE_TEXTDOMAIN ); ?></option>
			<option value="1" <?php selected( $option, 1 ); ?>><?php esc_html_e( 'Enabled', GPERSIANDATE_TEXTDOMAIN ); ?></option><?php
		echo '</select>';

		// echo '<p class="description">'. __( 'Select to enable current date and time on admin bar.', GPERSIANDATE_TEXTDOMAIN ).'</p>';
	}

	public function field_restrict_fromto( $args )
	{
		$field  = 'restrict_fromto';
		$option = isset( $this->options[$field] ) ? $this->options[$field] : 0;

		echo '<select name="gpersiandate['.$field.']" id="gpersiandate-'.$field.'">';
			?><option value="0" <?php selected( $option, 0 ); ?>><?php esc_html_e( 'Disabled', GPERSIANDATE_TEXTDOMAIN ); ?></option>
			<option value="1" <?php selected( $option, 1 ); ?>><?php esc_html_e( 'Enabled', GPERSIANDATE_TEXTDOMAIN ); ?></option><?php
		echo '</select>';

		// echo '<p class="description">'. __( 'Select to enable date picker on manage post screen.', GPERSIANDATE_TEXTDOMAIN ).'</p>';
	}

	public function field_restrict_month( $args )
	{
		$field  = 'restrict_month';
		$option = isset( $this->options[$field] ) ? $this->options[$field] : 0;

		echo '<select name="gpersiandate['.$field.']" id="gpersiandate-'.$field.'">';
			?><option value="0" <?php selected( $option, 0 ); ?>><?php esc_html_e( 'Disabled', GPERSIANDATE_TEXTDOMAIN ); ?></option>
			<option value="1" <?php selected( $option, 1 ); ?>><?php esc_html_e( 'Gregorian', GPERSIANDATE_TEXTDOMAIN ); ?></option>
			<option value="2" <?php selected( $option, 2 ); ?>><?php esc_html_e( 'Jalali', GPERSIANDATE_TEXTDOMAIN ); ?></option><?php
		echo '</select>';

		// echo '<p class="description">'. __( 'Select to enable date picker on manage post screen.', GPERSIANDATE_TEXTDOMAIN ).'</p>';
	}

	public function posts_where_persian_month( $where = '' )
	{
		if ( ! empty( $_REQUEST['persian_month'] ) ) {

			$persian_month = ''.preg_replace( '|[^0-9]|', '', $_REQUEST['persian_month'] );

			list( $first, $last ) = gPersianDateDate::monthFirstAndLast( substr( $persian_month, 0, 4 ), substr( $persian_month, 4, 2 ) );

			$where .= " AND post_date >='{$first}' AND post_date <='{$last}' ";
		}

		return $where;
	}

	public function posts_where_start_end( $where = '' )
	{
		if ( $start_date = gPersianDateDate::makeMySQLFromInput( self::req( 'persian_start_date' ) ) )
			$where .= " AND post_date >='{$start_date}' ";

		if ( $end_date = gPersianDateDate::makeMySQLFromInput( self::req( 'persian_end_date' ), 'Y-m-d' ) )
			$where .= " AND post_date <='{$end_date} 23:59:59'";

		return $where;
	}

	public function disable_months_dropdown( $false, $post_type )
	{
		// RESPECT OTHERS!
		if ( $false )
			return $false;

		// DISABLE
		if ( ! isset( $this->options['restrict_month'] )
			|| ! $this->options['restrict_month'] )
			return TRUE;

		// GREGORIAN
		if ( '1' == $this->options['restrict_month'] )
			return FALSE;

		if ( ! $months = gPersianDateWordPress::getPostTypeMonths( $post_type, $_GET ) )
			return TRUE;

		$persian_month = isset( $_GET['persian_month'] ) ? (int) $_GET['persian_month'] : 0;

		echo '<label for="filter-by-date" class="screen-reader-text">'
			._x( 'Filter by date', 'Admin: Months Dropdown', GPERSIANDATE_TEXTDOMAIN )
		.'</label>';

		echo '<select name="persian_month" id="filter-by-date">';

			echo '<option '.selected( $persian_month, 0, FALSE ).' value="0">'
				._x( 'All dates', 'Admin: Months Dropdown', GPERSIANDATE_TEXTDOMAIN )
			.'</option>';

			foreach ( $months as $key => $month )
				vprintf( '<option %s value="%s">%s</option>'."\n", [
					selected( $persian_month, $key, FALSE ),
					esc_attr( $key ),
					esc_html( $month ),
				] );

		echo '</select>';

		return TRUE;
	}

	public function restrict_manage_posts_start_end( $post_type, $which )
	{
		// TODO: set maximum and minimum date based on stored posts
		// list( $first, $last ) = gPersianDateWordPress::getPosttypeFirstAndLast( $post_type, $_GET );

		?><span class="gpersiandate-datepicker"><input
			type="text"
			name="persian_start_date"
			id="persian_start_date"
			value="<?php echo esc_attr( self::req( 'persian_start_date' ) ); ?>"
			placeholder="<?php esc_attr_e( 'From', GPERSIANDATE_TEXTDOMAIN ); ?>"
			autocomplete="off"
			data-persiandate="datepicker"
			<?php // echo 'data-min="'.date( 'c', strtotime( $first ) ).'"'; ?>
			<?php // echo 'data-max="'.date( 'c', strtotime( $last ) ).'"'; ?>
		/><span class="dashicons dashicons-calendar"></span></span><?php

		?><span class="gpersiandate-datepicker"><input
			type="text"
			name="persian_end_date"
			id="persian_end_date"
			value="<?php echo esc_attr( self::req( 'persian_end_date' ) ); ?>"
			placeholder="<?php esc_attr_e( 'To', GPERSIANDATE_TEXTDOMAIN ); ?>"
			autocomplete="off"
			data-persiandate="datepicker"
			<?php // echo 'data-min="'.date( 'c', strtotime( $first ) ).'"'; ?>
			<?php // echo 'data-max="'.date( 'c', strtotime( $last ) ).'"'; ?>
		/><span class="dashicons dashicons-calendar"></span></span><?php
	}
}
