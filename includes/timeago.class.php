<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateTimeAgo extends gPersianDateBase
{

	// @REF: http://timeago.yarp.com/
	// @REF: https://github.com/rmm5t/jquery-timeago

	const TIMEAGO_VERSION  = '1.5.4';
	const WP_SCRIPT_HANDLE = 'gpersiandate-timeago';

	public static function enqueue()
	{
		if ( wp_script_is( self::WP_SCRIPT_HANDLE ) )
			return self::WP_SCRIPT_HANDLE;

		$script = '';

		if ( 'en_US' != GPERSIANDATE_LOCALE ) {

			$defaults = wp_json_encode( array(
				'prefixAgo'     => NULL,
				'prefixFromNow' => NULL,
				'wordSeparator' => ' ',

				'suffixAgo'     => _x( 'ago', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'suffixFromNow' => _x( 'from now', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'inPast'        => _x( 'any moment now', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'seconds'       => _x( 'less than a minute', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'minute'        => _x( 'about a minute', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'minutes'       => _x( '%d minutes', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'hour'          => _x( 'about an hour', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'hours'         => _x( 'about %d hours', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'day'           => _x( 'a day', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'days'          => _x( '%d days', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'month'         => _x( 'about a month', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'months'        => _x( '%d months', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'year'          => _x( 'about a year', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),
				'years'         => _x( '%d years', 'Time Ago', GPERSIANDATE_TEXTDOMAIN ),

				// 'numbers'       => array_map( array( 'gPersianDateTranslate', 'numbers' ), range( 0, 9 ) ),
				'numbers'       => array(
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
				),
			) );

			$script .= "jQuery.timeago.settings.strings={$defaults};";
		}

		// cutoff : Return the original date if time distance is older than cutoff (miliseconds).
		// Display original dates older than 24 hours
		$script .= "jQuery.timeago.settings.cutoff=1000*60*60*24;";

		$script .= "jQuery(document).ready(function($){
			$('.do-timeago').timeago().removeClass('do-timeago');
		});";

		$variant = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( self::WP_SCRIPT_HANDLE, GPERSIANDATE_URL.'assets/libs/jquery-timeago/jquery.timeago'.$variant.'.js', array( 'jquery' ), self::TIMEAGO_VERSION );
		wp_add_inline_script( self::WP_SCRIPT_HANDLE, $script );

		return self::WP_SCRIPT_HANDLE;
	}
}
