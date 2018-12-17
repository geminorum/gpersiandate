<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateFormat extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	private static $_saved = [];

	protected function setup_actions()
	{
		self::$_saved = [
			'time' => get_option( 'time_format' ),
			'date' => get_option( 'date_format' ),
		];

		if ( is_admin() ) {

			add_filter( 'gettext', [ $this, 'gettext' ], 10, 3 );
			add_filter( 'gettext_with_context', [ $this, 'gettext_with_context' ], 10, 4 );

			add_filter( 'date_formats', [ $this, 'date_formats' ] );
			add_filter( 'time_formats', [ $this, 'time_formats' ] );
			add_filter( 'default_option_start_of_week', [ $this, 'default_option_start_of_week' ] );
		}

		add_filter( 'custom_date_formats', [ $this, 'custom_date_formats' ] );
		add_filter( 'gmember_date_formats', [ $this, 'custom_date_formats' ] );
	}

	// @SEE: http://php.net/manual/en/function.date.php
	// @SEE: date_i18n()
	// @REF: https://core.trac.wordpress.org/ticket/20973
	public static function checkISO( $format )
	{
		return in_array( $format, [
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
			'Y-m-d_G-i-s',
			'Y-m-d H:i:s',
			'Y-m-d G:i:s',
			'd-M-Y H:i',

			DATE_W3C, // eq `c`
			DATE_ISO8601, // eq `c`
			DATE_RFC2822, // eq `r`
			'Y-m-d\TH:i:s+00:00', // eq `DATE_W3C` @SEE: http://jochenhebbrecht.be/site/node/761
			'Y-m-d\TH:i:sP',
		] );
	}

	public static function checkTimeOnly( $format )
	{
		return in_array( $format, [
			'H:i',
			'G:i',
			'g:i',
			'H:i:s',
			'G:i:s',
		] );
	}

	public static function sanitize( $format = '', $context = 'date', $locale = GPERSIANDATE_LOCALE )
	{
		if ( '' == $format && isset( self::$_saved[$context] ) )
			$format = self::$_saved[$context];

		$formats = apply_filters( 'gpersiandate_sanitize_format', [
			'j M, Y' => 'j M Y',
			'F j, Y' => 'j F Y',
			'd. F Y' => 'j F Y',
		], $context, $locale );

		if ( isset( $formats[$format] ) )
			return $formats[$format];

		return $format;
	}

	public function gettext_with_context( $translations, $text, $context, $domain )
	{
		if ( 'default' != $domain )
			return $translations;

		static $strings;

		if ( empty( $strings ) )
			$strings = [
				'dashboard' => [
					'%1$s, %2$s' => '%1$s &mdash; %2$s', // `wp_dashboard_recent_posts()`
				],
				'revision date format' => [
					'F j, Y @ H:i:s' => 'j M Y — H:i', // `wp_post_revision_title_expanded()`
				],
			];

		if ( isset( $strings[$context][$text] ) )
			return $strings[$context][$text];

		return $translations;
	}

	public function gettext( $translations, $text, $domain )
	{
		if ( 'default' != $domain )
			return $translations;

		static $strings;

		if ( empty( $strings ) )
			$strings = [

				// '%1$s %2$s, %3$s @ %4$s:%5$s' => ( 'fa_IR' == GPERSIANDATE_LOCALE ? '%2$s%1$s%3$s @ %5$s:%4$s' : '%2$s%1$s%3$s @ %4$s:%5$s' ), // `touch_time()`

				'M jS'           => 'j M Y', // `wp_dashboard_recent_posts()`
				'M jS Y'         => 'j M Y', // `wp_dashboard_recent_posts()`
				'F j, Y'         => 'j M Y',
				'M j, Y @ H:i'   => 'j M Y @ H:i',
				'M j, Y g:i a'   => 'j M Y g:i a', // Abbreviated

				'Howdy, %s' => '%s', // `wp_admin_bar_my_account_item()`

				// '%1$s MB (%2$s%%) Space Used' => sprintf( _x( '%s Space Used', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ), '<span title="&lrm;%1$s MB&rlm;">%2$s%%</span>' ),
				// '%1$s MB (%2$s%%) Space Used' => sprintf( _x( '%s Space Used', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
				// 	'<span title="&lrm;%s MB&rlm;">%s'.( is_rtl() ? '&#1642;' : '&#37;' ).'</span>' ), // FIXME: is_rtl not working this early

				// working but pathetic!
				'Caption'                 => _x( 'Caption', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
				'Published on: <b>%s</b>' => _x( 'Published: <b>%s</b>', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
				'Original dimensions %s'  => _x( 'Original dimensions <span>%s</span>', 'gettext overrides', GPERSIANDATE_TEXTDOMAIN ),
			];

		if ( isset( $strings[$text] ) )
			return $strings[$text];

		return $translations;
	}

	// @SEE: http://codex.wordpress.org/Formatting_Date_and_Time
	public function date_formats( $formats )
	{
		// TODO : what about local?
		return [
			'j F Y',
			'y/n/d',
			'y/m/d',
			'Y/n/d',
			'Y/m/d',
			// 'l S F Y', // TODO: must support "l" : (st, nd or th in the 1st, 2nd or 15th.)
			__( 'F j, Y' ),
		];
	}

	public function time_formats( $formats )
	{
		return [
			'H:i',
			// 'g:i A',
			__( 'g:i a' ),
		];
	}

	public function default_option_start_of_week( $value )
	{
		return 6;
	}

	// @SEE: [Arabic Date Separator U-060D](https://github.com/rastikerdar/vazir-font/issues/81)
	public function custom_date_formats( $formats )
	{
		$formats['fulltime'] = 'l، j M Y - G:i';
		$formats['datetime'] = 'j F Y @ G:i';
		$formats['dateonly'] = 'l، j M Y';
		$formats['timedate'] = is_rtl() ? 'j F Y — H:i' : 'H:i — j F Y';
		$formats['monthday'] = is_rtl() ? 'j/n' : 'n/j';
		$formats['default']  = 'Y/m/d';

		return $formats;
	}
}
