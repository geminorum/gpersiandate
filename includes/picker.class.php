<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePicker extends gPersianDateBase
{

	const PERSIANDATE_VERSION = '0.7.1';
	const JQUERYUI_VERSION    = '1.9.1';
	const WP_SCRIPT_HANDLE    = 'gpersiandate-datepicker';

	public static function enqueue( $format = NULL )
	{
		if ( wp_script_is( self::WP_SCRIPT_HANDLE ) )
			return self::WP_SCRIPT_HANDLE;

		global $wp_locale;

		$days = gPersianDateStrings::dayoftheweek( NULL, TRUE, 'Jalali', FALSE );

		$defaults = wp_json_encode( [
			'closeText'   => _x( 'Done', 'Date Picker', 'gpersiandate' ),
			'currentText' => _x( 'Today', 'Date Picker', 'gpersiandate' ),
			'nextText'    => _x( 'Next', 'Date Picker', 'gpersiandate' ),
			'prevText'    => _x( 'Previous', 'Date Picker', 'gpersiandate' ),

			'dayNames'        => array_values( $days ),
			'dayNamesShort'   => array_values( $days ),
			'dayNamesMin'     => array_values( gPersianDateStrings::dayoftheweek( NULL, TRUE, 'Jalali', TRUE ) ),
			'monthNames'      => array_values( gPersianDateStrings::month( NULL, TRUE, 'Jalali', FALSE ) ),
			'monthNamesShort' => array_values( gPersianDateStrings::month( NULL, TRUE, 'Jalali', TRUE ) ),

			'isRTL'      => $wp_locale->is_rtl(),
			'dateFormat' => is_null( $format ) ? 'yy/mm/dd' : $format,
			'firstDay'   => absint( get_option( 'start_of_week' ) ), // 6

			// 'showMonthAfterYear' => FALSE,
			// 'yearSuffix'         => '',
		] );

		$lang = 'fa_IR' == GPERSIANDATE_LOCALE ? "'fa'" : 'null';

		// FIXME: our date picker does not support min/max
		/***
		// p = new PersianDate,
		// n = i.data('min') ? p.fromISOString(i.data('min')) : null,
		// x = i.data('max') ? p.fromISOString(i.data('max')) : null;

		// minDate: n,
		// maxDate: x,

		**/

		$script = "jQuery(document).ready(function($){"
			."var defaults={$defaults};defaults.calculateWeek=PersianDate.calculateWeek;defaults.calendar=PersianDate;"
			."$.datepicker.setDefaults(defaults,{$lang});"
			."$('[data-persiandate=\'datepicker\']').each(function(){"
				."$(this).datepicker();"
			."});"
		."});";

		$variant = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script( 'gpersiandate-persiandate', GPERSIANDATE_URL.'assets/libs/persiandate/persiandate'.$variant.'.js', [ 'jquery' ], self::PERSIANDATE_VERSION );
		wp_enqueue_script( self::WP_SCRIPT_HANDLE, GPERSIANDATE_URL.'assets/libs/persiandate/persiandate-datepicker'.$variant.'.js', [ 'jquery', 'gpersiandate-persiandate' ], self::JQUERYUI_VERSION );
		wp_add_inline_script( self::WP_SCRIPT_HANDLE, $script );

		wp_enqueue_style( self::WP_SCRIPT_HANDLE, GPERSIANDATE_URL.'assets/css/all.datepicker.css', [], GPERSIANDATE_VERSION );
		wp_style_add_data( self::WP_SCRIPT_HANDLE, 'rtl', 'replace' );

		return self::WP_SCRIPT_HANDLE;
	}
}
