<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateTimeAgo extends gPersianDateBase
{

	// @REF: http://timeago.yarp.com/
	// @REF: https://github.com/rmm5t/jquery-timeago

	const TIMEAGO_VERSION  = '1.6.7';
	const WP_SCRIPT_HANDLE = 'gpersiandate-timeago';

	public static function enqueue()
	{
		if ( wp_script_is( self::WP_SCRIPT_HANDLE ) )
			return self::WP_SCRIPT_HANDLE;

		$script = '';

		if ( 'en_US' != GPERSIANDATE_LOCALE ) {

			$strings = [
				'prefixAgo'     => NULL,
				'prefixFromNow' => NULL,
				'wordSeparator' => ' ',

				'suffixAgo'     => _x( 'ago', 'Time Ago', 'gpersiandate' ),
				'suffixFromNow' => _x( 'from now', 'Time Ago', 'gpersiandate' ),
				'inPast'        => _x( 'any moment now', 'Time Ago', 'gpersiandate' ),
				'seconds'       => _x( 'less than a minute', 'Time Ago', 'gpersiandate' ),
				'minute'        => _x( 'about a minute', 'Time Ago', 'gpersiandate' ),
				/* translators: %d: number of minutes */
				'minutes'       => _x( '%d minutes', 'Time Ago', 'gpersiandate' ),
				'hour'          => _x( 'about an hour', 'Time Ago', 'gpersiandate' ),
				/* translators: %d: number of hours */
				'hours'         => _x( 'about %d hours', 'Time Ago', 'gpersiandate' ),
				'day'           => _x( 'a day', 'Time Ago', 'gpersiandate' ),
				/* translators: %d: number of days */
				'days'          => _x( '%d days', 'Time Ago', 'gpersiandate' ),
				'month'         => _x( 'about a month', 'Time Ago', 'gpersiandate' ),
				/* translators: %d: number of months */
				'months'        => _x( '%d months', 'Time Ago', 'gpersiandate' ),
				'year'          => _x( 'about a year', 'Time Ago', 'gpersiandate' ),
				/* translators: %d: number of years */
				'years'         => _x( '%d years', 'Time Ago', 'gpersiandate' ),
			];

			if ( 'fa_IR' == GPERSIANDATE_LOCALE ) {

				// $strings['numbers'] = array_map( [ 'gPersianDateTranslate', 'numbers' ), range( 0, 9 ) );
				$strings['numbers'] = [
					'0' => chr(0xDB).chr(0xB0),
					'1' => chr(0xDB).chr(0xB1),
					'2' => chr(0xDB).chr(0xB2),
					'3' => chr(0xDB).chr(0xB3),
					'4' => chr(0xDB).chr(0xB4),
					'5' => chr(0xDB).chr(0xB5),
					'6' => chr(0xDB).chr(0xB6),
					'7' => chr(0xDB).chr(0xB7),
					'8' => chr(0xDB).chr(0xB8),
					'9' => chr(0xDB).chr(0xB9),
				];
			}

			$encoded = wp_json_encode( $strings );

			$script.= "jQuery.timeago.settings.strings={$encoded};";
		}

		// display original dates older than 24 hours
		// cutoff: returns the original date if time distance is older than cutoff (miliseconds)
		$script.= "jQuery.timeago.settings.cutoff=1000*60*60*24*7;";

		$script.= "jQuery(document).ready(function($){
			$('.do-timeago').timeago().removeClass('do-timeago');
		});";

		$variant = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( self::WP_SCRIPT_HANDLE, GPERSIANDATE_URL.'assets/libs/jquery-timeago/jquery.timeago'.$variant.'.js', [ 'jquery' ], self::TIMEAGO_VERSION );
		wp_add_inline_script( self::WP_SCRIPT_HANDLE, $script );

		return self::WP_SCRIPT_HANDLE;
	}
}
