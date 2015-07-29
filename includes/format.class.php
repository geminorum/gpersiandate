<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateFormat extends gPersianDateModuleCore
{

	var $_ajax = TRUE;

	private static $_saved = array();

	protected function setup_actions()
	{
		self::$_saved = array(
			'time' => get_option( 'time_format' ),
			'date' => get_option( 'date_format' ),
		);

		if ( is_admin() ) {
			// ? : must be admin?
			add_filter( 'gettext', array( &$this, 'gettext' ), 10, 3 );
			//add_filter( 'gettext_with_context', array( &$this, 'gettext_with_context' ), 10, 4 ); // no need for now

			add_filter( 'date_formats', array( &$this, 'date_formats' ) );
			add_filter( 'time_formats', array( &$this, 'time_formats' ) );
			add_filter( 'pre_option_start_of_week', array( &$this, 'pre_option_start_of_week' ) );
			add_filter( 'default_option_start_of_week', array( &$this, 'pre_option_start_of_week' ) );
		}
	}

	public static function checkISO( $format )
	{
		$iso = array(
			'U',
			'r',
			'c',
			'G',
			'Y-m-d_H-i-s',
			'Y-m-d H:i:s',
			// 'Y-m-d\TH:i:s\Z', // WTF: wierd behavior
		);

		if ( in_array( $format, $iso ) )
			return TRUE;

		return FALSE;
	}

	// before : format()
	public static function sanitize( $format = '', $context = 'date', $locale = GPERSIANDATE_LOCALE )
	{
		if ( '' == $format && isset( self::$_saved[$context] ) )
			$format = self::$_saved[$context];

		$formats = apply_filters( 'gpersiandate_sanitize_format', array(
			'j M, Y' => 'j M Y',
			'F j, Y' => 'j F Y',
		), $context, $locale );

		if ( isset( $formats[$format] ) )
			return $formats[$format];

		return $format;
	}

	public function gettext_with_context( $translations, $text, $context, $domain )
	{
		return $this->gettext( $translations, $text, $domain );
	}

	public function gettext( $translations, $text, $domain )
	{
		if ( 'default' != $domain )
			return $translations;

		$strings = apply_filters( 'gpersiandate_gettext', array(

			// on touch_time()
			/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
			'%1$s %2$s, %3$s @ %4$s : %5$s' => ( 'fa_IR' == GPERSIANDATE_LOCALE ? '%2$s%1$s%3$s @ %5$s:%4$s' : '%2$s%1$s%3$s @ %4$s:%5$s' ),

			/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
			'Y-m-d G:i:s' => 'G:i:s Y-m-d',

			// ADMIN DAHSBOARD ACTIVITY WIDGET
			/* translators: date and time format for recent posts on the dashboard, see http://php.net/date */
			'M jS' => 'j M Y',
			/* translators: 1: relative date, 2: time, 3: post edit link, 4: post title */
			'<span>%1$s, %2$s</span> <a href="%3$s">%4$s</a>' => '<span>%1$s &ndash; %2$s</span> <a href="%3$s">%4$s</a>',

			'Howdy, %1$s' => '%1$s',

		), $domain );

		if ( isset( $strings[$text] ) )
			return $strings[$text];

		return $translations;
	}

	// FORMATS : http://codex.wordpress.org/Formatting_Date_and_Time
	public function date_formats( $formats )
	{
		// TODO : what about local?
		return array(
			'j F Y',
			'y/n/d',
			'y/m/d',
			'Y/n/d',
			'Y/m/d',
			// 'l S F Y', // TODO : must support "l" : (st, nd or th in the 1st, 2nd or 15th.)
			__( 'F j, Y' ),
		);
	}

	public function time_formats( $formats )
	{
		return array(
			'H:i',
			// 'g:i A',
			__('g:i a'),
		);
	}

	public function pre_option_start_of_week( $value )
	{
		return 6;
	}

}
