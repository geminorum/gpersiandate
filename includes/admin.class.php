<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateAdmin extends gPersianDateModuleCore
{

	protected $options    = array();
	protected $datepicker = TRUE;

	protected function setup_actions()
	{
		if ( ! is_admin() )
			return;

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'update_footer', array( 'gPersianDateTranslate', 'html' ), 12 );

		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function admin_init()
	{
		$this->options = gPersianDate()->options();

		$page = 'general';

		register_setting( $page, 'gpersiandate', array( $this, 'settings_sanitize' ) );

		add_settings_field( 'adminbar_clock', __( 'Adminbar Clock', GPERSIANDATE_TEXTDOMAIN ), array( $this, 'field_adminbar_clock' ), $page );
		add_settings_field( 'restrict_month', __( 'Month Restrictions', GPERSIANDATE_TEXTDOMAIN ), array( $this, 'field_restrict_month' ), $page );
		add_settings_field( 'restrict_fromto', __( 'Date Restrictions', GPERSIANDATE_TEXTDOMAIN ), array( $this, 'field_restrict_fromto' ), $page );
	}

	public function settings_sanitize( $input )
	{
		$output = array();

		if ( isset( $input['adminbar_clock'] ) && $input['adminbar_clock'] )
			$output['adminbar_clock'] = 1;

		if ( isset( $input['restrict_month'] ) && $input['restrict_month'] )
			$output['restrict_month'] = $input['restrict_month'];

		if ( isset( $input['restrict_fromto'] ) && $input['restrict_fromto'] )
			$output['restrict_fromto'] = 1;

		return $output;
	}

	public function field_adminbar_clock( $args )
	{
		$field  = 'adminbar_clock';
		$option = isset( $this->options[$field] ) ? $this->options[$field] : 0;

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

	public function admin_enqueue_scripts()
	{
		$screen = get_current_screen();

		if ( 'edit' == $screen->base
			&& isset( $this->options['restrict_fromto'] )
			&& $this->options['restrict_fromto'] ) {

			wp_register_script( 'persianDatepicker', GPERSIANDATE_URL.'assets/js/persianDatepicker-edited.min.js', array( 'jquery' ), '0.1.0' );

			wp_enqueue_script( 'gpersiandate-adminedit',
				GPERSIANDATE_URL.'assets/js/admin.edit.js',
				array( 'jquery', 'persianDatepicker' ),
				GPERSIANDATE_VERSION, TRUE );

			add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts_start_end' ) );
		}
	}

	public function admin_print_styles()
	{
		echo '<link rel="stylesheet" href="'.GPERSIANDATE_URL.'assets/libs/persianDatepicker/css/persianDatepicker-default.css" type="text/css" />';
		echo '<link rel="stylesheet" href="'.GPERSIANDATE_URL.'assets/css/admin.edit.css" type="text/css" />';
	}

	public function pre_get_posts( $query )
	{
		global $pagenow;

		if ( $query->is_admin && ( 'edit.php' == $pagenow ) ) {
			if ( isset( $_REQUEST['start_date_gp'] ) || isset( $_REQUEST['end_date_gp'] ) )
				add_filter( 'posts_where', array( $this, 'posts_where_start_end' ) );

			if ( isset( $_REQUEST['mgp'] ) && 0 != $_REQUEST['mgp'] )
				add_filter( 'posts_where', array( $this, 'posts_where_mgp' ) );
		}

		return $query;
	}

	public function posts_where_mgp( $where = '' )
	{
		if ( isset( $_REQUEST['mgp'] )
			&& ! empty( $_REQUEST['mgp'] ) ) {

			$mgp = ''.preg_replace( '|[^0-9]|', '', $_REQUEST['mgp'] );

			list( $first, $last ) = gPersianDateDate::monthFirstAndLast( substr( $mgp, 0, 4 ), substr( $mgp, 4, 2 ) );

			$where .= " AND post_date >='$first' AND post_date <='$last' ";
		}

		return $where;
	}

	public function posts_where_start_end( $where = '' )
	{
		global $wpdb;

		if ( isset( $_REQUEST['start_date_gp'] ) && ! empty( $_REQUEST['start_date_gp'] ) ) {
			$start      = explode( '/', $_REQUEST['start_date_gp'] );
			$start_date = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, $start[1], $start[2], $start[0] ) );
			$where .= " AND post_date >='$start_date' ";
		}

		if ( isset( $_REQUEST['end_date_gp'] ) && ! empty( $_REQUEST['end_date_gp'] ) ) {
			$end      = explode( '/', $_REQUEST['end_date_gp'] );
			$end_date = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, $end[1], $end[2], $end[0] ) );
			$where .= " AND post_date <='$end_date' ";
		}

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

		if ( ! $months = gPersianDateDate::getPosttypeMonths( $post_type ) )
			return TRUE;

		$mgp = isset( $_GET['mgp'] ) ? (int) $_GET['mgp'] : 0;

		echo '<select name="mgp" id="gpersiandate-mgp">';
			echo '<option '.selected( $mgp, 0, FALSE ).' value="0">'. __( 'Show all dates', GPERSIANDATE_TEXTDOMAIN ).'</option>';

			foreach ( $months as $key => $month )
				vprintf( '<option %s value="%s">%s</option>'."\n", array(
					selected( $mgp, $key, FALSE ),
					esc_attr( $key ),
					esc_html( $month ),
				) );

		echo '</select>';

		return TRUE;
	}

	public function restrict_manage_posts_start_end()
	{
		// TODO: set maximum and minimum date based on stored posts

		$start_date = isset( $_REQUEST['start_date_gp'] ) ? $_REQUEST['start_date_gp'] : '';
		$end_date   = isset( $_REQUEST['end_date_gp']   ) ? $_REQUEST['end_date_gp']   : '';

		?><span class="gpersiandate-datepicker"><input type="text" name="start_date_gp" id="start_date_gp" class="datepick" value="<?php echo $start_date;?>"
		placeholder="<?php esc_attr_e( 'From', GPERSIANDATE_TEXTDOMAIN ); ?>" /></span> <?php

		?><span class="gpersiandate-datepicker"><input type="text" name="end_date_gp" id="end_date_gp" class="datepick" value="<?php echo $end_date;?>"
		placeholder="<?php esc_attr_e( 'To', GPERSIANDATE_TEXTDOMAIN ); ?>" /></span> <?php
	}
}
