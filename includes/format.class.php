<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateFormat extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	private static $_saved = array();

	protected function setup_actions()
	{
		self::$_saved = array(
			'time' => get_option( 'time_format' ),
			'date' => get_option( 'date_format' ),
		);

		if ( is_admin() ) {

			add_filter( 'gettext', array( $this, 'gettext' ), 10, 3 );
			add_filter( 'gettext_with_context', array( $this, 'gettext_with_context' ), 10, 4 );

			add_filter( 'date_formats', array( $this, 'date_formats' ) );
			add_filter( 'time_formats', array( $this, 'time_formats' ) );
			add_filter( 'pre_option_start_of_week', array( $this, 'pre_option_start_of_week' ) );
			add_filter( 'default_option_start_of_week', array( $this, 'pre_option_start_of_week' ) );
		}
	}

	// @SEE: http://php.net/manual/en/function.date.php
	// @SEE: date_i18n()
	public static function checkISO( $format )
	{
		return in_array( $format, array(
			'Z', // Timezone offset in seconds // -43200 through 50400
			'T', // Timezone abbreviation // Examples: EST, MDT
			'O', // Difference to Greenwich time (GMT) in hours // Example: +0200
			'P', // Difference to Greenwich time (GMT) with colon between hours and minutes // Example: +02:00
			'U', // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
			'u', // Microseconds // Example: 654321
			'e', // Timezone identifier // Examples: UTC, GMT, Atlantic/Azores
			'r', // RFC 2822 formatted date // Example: Thu, 21 Dec 2000 16:01:07 +0200
			'c', // ISO 8601 date // 2004-02-12T15:19:21+00:00 // 'Y-m-d\TH:i:s\Z'
			'G', // 24-hour format of an hour without leading zeros // 0 through 23
			'I', // Whether or not the date is in daylight saving time // 1 if Daylight Saving Time, 0 otherwise.

			'Y-m-d_H-i-s',
			'Y-m-d H:i:s',
		) );
	}

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
		if ( 'default' != $domain )
			return $translations;

		$strings = array(
			'dashboard' => array(
				'%1$s, %2$s' => '%1$s &mdash; %2$s', // `wp_dashboard_recent_posts()`
			),
			'revision date format' => array(
				'F j, Y @ H:i:s' => 'j M Y — H:i', // `wp_post_revision_title_expanded()`
			),
		);

		if ( isset( $strings[$context][$text] ) )
			return $strings[$context][$text];

		return $translations;
	}

	public function gettext( $translations, $text, $domain )
	{
		if ( 'default' != $domain )
			return $translations;

		$strings = array(

			// '%1$s %2$s, %3$s @ %4$s:%5$s' => ( 'fa_IR' == GPERSIANDATE_LOCALE ? '%2$s%1$s%3$s @ %5$s:%4$s' : '%2$s%1$s%3$s @ %4$s:%5$s' ), // `touch_time()`

			'M jS'           => 'j M Y', // `wp_dashboard_recent_posts()`
			'M jS Y'         => 'j M Y', // `wp_dashboard_recent_posts()`
			'F j, Y'         => 'j M Y',
			'M j, Y @ H:i'   => 'j M Y @ H:i',

			'Howdy, %s' => '%s', // `wp_admin_bar_my_account_item()`

			'Caption' => _x( 'Caption', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
			'Published on: <b>%1$s</b>' => _x( 'Published: <b>%1$s</b>', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
		);

		if ( isset( $strings[$text] ) )
			return $strings[$text];

		return $translations;
	}

	// @SEE: http://codex.wordpress.org/Formatting_Date_and_Time
	public function date_formats( $formats )
	{
		// TODO : what about local?
		return array(
			'j F Y',
			'y/n/d',
			'y/m/d',
			'Y/n/d',
			'Y/m/d',
			// 'l S F Y', // TODO: must support "l" : (st, nd or th in the 1st, 2nd or 15th.)
			__( 'F j, Y' ),
		);
	}

	public function time_formats( $formats )
	{
		return array(
			'H:i',
			// 'g:i A',
			__( 'g:i a' ),
		);
	}

	public function pre_option_start_of_week( $value )
	{
		return 6;
	}
}
