<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePicker extends gPersianDateBase
{

	const PERSIANDATE_VERSION = '0.7.1';
	const JQUERYUI_VERSION    = '1.9.1';

	// FIXME: WORKING DRAFT
	public static function enqueue( $format = NULL )
	{
		global $wp_locale;

		// $datepicker_date_format = str_replace(
		// 	array(
		// 		'd', 'j', 'l', 'z', // Day.
		// 		'F', 'M', 'n', 'm', // Month.
		// 		'Y', 'y'            // Year.
		// 	),
		// 	array(
		// 		'dd', 'd', 'DD', 'o',
		// 		'MM', 'M', 'm', 'mm',
		// 		'yy', 'y'
		// 	),
		// 	get_option( 'date_format' )
		// );

		$months = array_values( gPersianDateStrings::month( NULL, TRUE ) );
		$days   = gPersianDateStrings::dayoftheweek( NULL, TRUE );
		$min    = gPersianDateStrings::dayoftheweek( NULL, TRUE, NULL, TRUE );

		$days = array(
			$days[1],
			$days[2],
			$days[3],
			$days[4],
			$days[5],
			$days[6],
			$days[0],
		);

		$min = array(
			$min[1],
			$min[2],
			$min[3],
			$min[4],
			$min[5],
			$min[6],
			$min[0],
		);

		$defaults = wp_json_encode( array(
			'closeText'       => _x( 'Done', 'Date Picker', GPERSIANDATE_TEXTDOMAIN ),
			'currentText'     => _x( 'Today', 'Date Picker', GPERSIANDATE_TEXTDOMAIN ),

			'monthNames'      => $months,
			'monthNamesShort' => $months, // FIXME: use month_abbrev

			'nextText'        => _x( 'Next', 'Date Picker', GPERSIANDATE_TEXTDOMAIN ),
			'prevText'        => _x( 'Previous', 'Date Picker', GPERSIANDATE_TEXTDOMAIN ),

			'dayNames'        => $days,
			'dayNamesShort'   => $days,
			'dayNamesMin'     => $min,

			// 'dateFormat'      => $datepicker_date_format,
			// 'firstDay'        => absint( get_option( 'start_of_week' ) ),
			'isRTL'           => $wp_locale->is_rtl(),

			'dateFormat' => 'yy/mm/dd',
			'firstDay'   => 6,

			// showMonthAfterYear: false,
			// yearSuffix: '',
		) );

		$lang    = 'fa_IR' == GPERSIANDATE_LOCALE ? "'fa'" : 'null';
		$variant = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script( 'gpersiandate-persiandate', GPERSIANDATE_URL.'assets/libs/persiandate/persiandate'.$variant.'.js', array( 'jquery' ), self::PERSIANDATE_VERSION );
		wp_enqueue_script( 'gpersiandate-datepicker', GPERSIANDATE_URL.'assets/libs/persiandate/persiandate-datepicker'.$variant.'.js', array( 'jquery', 'gpersiandate-persiandate' ), self::JQUERYUI_VERSION );

		wp_enqueue_style( 'gpersiandate-datepicker', GPERSIANDATE_URL.'assets/css/all.datepicker.css', array(), GPERSIANDATE_VERSION );

		// FIXME: our date picker does not support min/max
		/***
		// p = new PersianDate,
		// n = i.data('min') ? p.fromISOString(i.data('min')) : null,
		// x = i.data('max') ? p.fromISOString(i.data('max')) : null;

		// minDate: n,
		// maxDate: x,

		**/

		wp_add_inline_script( 'gpersiandate-datepicker', "jQuery(document).ready(function(jQuery){jQuery.datepicker.setDefaults({$defaults},{$lang});jQuery('[data-persiandate=\'datepicker\']').each(function(){jQuery(this).datepicker({calculateWeek:PersianDate.calculateWeek,calendar:PersianDate});});});" );
	}
}
