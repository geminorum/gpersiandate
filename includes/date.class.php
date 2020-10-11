<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDate extends gPersianDateModuleCore
{

	public static function fromObject( $format, $datetime = NULL, $timezone_string = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		if ( is_null( $datetime ) ) {

			$timestamp = time();

			if ( ! $timezone_string )
				$timezone_string = gPersianDateTimeZone::current();

			$datetime = date_create( '@'.$timestamp );
			$datetime->setTimezone( new \DateTimeZone( $timezone_string ) );

		} else if ( ! is_a( $datetime, 'DateTime' )
			&& ! is_a( $datetime, 'DateTimeImmutable' ) ) {

			return FALSE;
		}

		if ( defined( 'GPERSIANDATE_DISABLE_CONVERSION' ) && GPERSIANDATE_DISABLE_CONVERSION )
			return $datetime->format( $format );

		if ( gPersianDateFormat::checkISO( $format ) )
			return $datetime->format( $format );

		if ( gPersianDateFormat::checkTimeOnly( $format ) )
			$string = $datetime->format( $format );

		else
			$string = gPersianDateDateTime::to( $datetime, $format, $timezone_string, $calendar );

		if ( is_null( $translate ) )
			$translate = gPersianDateFormat::checkTranslate( $format );

		if ( $translate )
			return gPersianDateTranslate::numbers( $string, $locale );

		return $string;
	}

	// not translating!
	public static function _fromObject( $format, $datetime = NULL, $timezone_string = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		return self::fromObject( $format, $datetime, $timezone_string, $locale, FALSE, $calendar );
	}

	// @REF: `mysql2date()`
	public static function toObject( $date, $timezone_string = GPERSIANDATE_TIMEZONE )
	{
		if ( empty( $date ) || '0000-00-00 00:00:00' === $date )
			return FALSE;

		if ( is_numeric( $date ) ) {

			$datetime = date_create( '@'.$date );
			$timezone = new \DateTimeZone( $timezone_string );
			return $datetime->setTimezone( $timezone );
		}

		return date_create( $date, new \DateTimeZone( $timezone_string ) );
	}

	// FIXME: DEPRECATED
	// for back comp only
	public static function to( $format, $datetime = NULL, $timezone_string = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		self::_dev_dep( 'gPersianDateDate::fromObject()' );

		if ( FALSE === $datetime )
			return FALSE;

		if ( ! is_null( $datetime )
			&& ! is_a( $datetime, 'DateTime' )
			&& ! is_a( $datetime, 'DateTimeImmutable' ) ) {

			$datetime = self::toObject( $datetime, $timezone_string );
		}

		return self::fromObject( $format, $datetime, $timezone_string, $locale, $translate, $calendar );
	}

	// FIXME: DROP THIS
	public static function to_OLD( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		if ( FALSE === $time )
			return FALSE;

		if ( is_null( $time ) )
			$time = current_time( 'mysql' );

		if ( defined( 'GPERSIANDATE_DISABLE_CONVERSION' ) && GPERSIANDATE_DISABLE_CONVERSION )
			return mysql2date( $format, $time, FALSE );

		if ( gPersianDateFormat::checkISO( $format ) )
			return mysql2date( $format, $time, FALSE );

		if ( gPersianDateFormat::checkTimeOnly( $format ) )
			$string = mysql2date( $format, $time, FALSE );

		else
			$string = gPersianDateDateTime::to( $time, $format, $timezone, $calendar );

		if ( is_null( $translate ) )
			$translate = gPersianDateFormat::checkTranslate( $format );

		if ( $translate )
			return gPersianDateTranslate::numbers( $string, $locale );

		return $string;
	}

	// not translating!
	public static function _to( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		return self::to( $format, $time, $timezone, $locale, FALSE, $calendar );
	}

	public static function toByCal( $format, $time = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		return self::to( $format, $time, GPERSIANDATE_TIMEZONE, NULL, $translate, $calendar );
	}

	public static function fromObjectHijri( $format, $datetime = NULL, $timezone_string = GPERSIANDATE_TIMEZONE, $locale = 'ar', $translate = NULL )
	{
		return self::fromObject( $format, $datetime, $timezone_string, $locale, $translate, 'Hijri' );
	}

	public static function toHijri( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = 'ar', $translate = NULL )
	{
		self::_dev_dep( 'gPersianDateDate::fromObjectHijri()' );

		return self::to( $format, $time, $timezone, $locale, $translate, 'Hijri' );
	}

	// @REF: http://php.net/manual/en/function.getdate.php
	public static function get( $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = NULL, $translate = FALSE, $calendar = 'Jalali' )
	{
		if ( FALSE === $time )
			return [];

		$string = self::to( 's|i|G|j|w|n|Y|z|l|F', $time, $timezone, $locale, FALSE, $calendar );

		if ( $translate )
			$string = gPersianDateTranslate::numbers( $string, $locale );

		$array = explode( '|', $string );

		return [
			'seconds' => $array[0], // `s`: Numeric representation of seconds: 0 to 59
			'minutes' => $array[1], // `i`: Numeric representation of minutes: 0 to 59
			'hours'   => $array[2], // `G`: Numeric representation of hours: 0 to 23
			'mday'    => $array[3], // `j`: Numeric representation of the day of the month: 1 to 31
			'wday'    => $array[4], // `w`: Numeric representation of the day of the week: 0 (for Sunday) through 6 (for Saturday)
			'mon'     => $array[5], // `n`: Numeric representation of a month: 1 through 12
			'year'    => $array[6], // `Y`: A full numeric representation of a year, 4 digits: Examples: 1999 or 2003
			'yday'    => $array[7], // `z`: Numeric representation of the day of the year: 0 through 365
			'weekday' => $array[8], // `l`: A full textual representation of the day of the week: Sunday through Saturday
			'month'   => $array[9], // `F`: A full textual representation of a month, such as January or March: January through December

			0 => is_null( $time ) ? current_time( 'timestamp' ) : $time,
		];
	}

	public static function getByCal( $time = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		return self::get( $time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, $translate, $calendar );
	}

	public static function make( $hour, $minute, $second, $month, $day, $year, $calendar = 'Jalali', $timezone = GPERSIANDATE_TIMEZONE )
	{
		return gPersianDateDateTime::make( $hour, $minute, $second, $month, $day, $year, $calendar, $timezone );
	}

	public static function makeMySQL( $hour, $minute, $second, $jmonth, $jday, $jyear, $calendar = 'Jalali', $timezone = GPERSIANDATE_TIMEZONE )
	{
		return date( 'Y-m-d H:i:s', self::make( $hour, $minute, $second, $jmonth, $jday, $jyear, $calendar, $timezone ) );
	}

	public static function makeFromArray( $array = [] )
	{
		$parts = self::atts( [
			'year'     => 1362, // ;)
			'month'    => 1,
			'day'      => 1,
			'hour'     => 0,
			'minute'   => 0,
			'second'   => 0,
			'calendar' => 'Jalali',
			'timezone' => GPERSIANDATE_TIMEZONE,
		], $array );

		return self::make(
			$parts['hour'],
			$parts['minute'],
			$parts['second'],
			$parts['month'],
			$parts['day'],
			$parts['year'],
			$parts['calendar'],
			$parts['timezone']
		);
	}

	public static function makeMySQLFromArray( $array = [], $format = NULL, $fallback = '' )
	{
		if ( empty( $array ) )
			return $fallback;

		if ( is_null( $format ) )
			$format = 'Y-m-d H:i:s';

		return date( $format, self::makeFromArray( $array ) );
	}

	public static function makeFromInput( $input, $calendar = 'Jalali', $timezone = GPERSIANDATE_TIMEZONE, $fallback = '' )
	{
		if ( empty( $input ) )
			return $fallback;

		// FIXME: needs sanity checks
		$parts = explode( '/', str_replace( [ '-', '\\' ], '/', apply_filters( 'string_format_i18n_back', $input ) ) );

		return self::make( 0, 0, 0, $parts[1], $parts[2], $parts[0], $calendar, $timezone );
	}

	public static function makeMySQLFromInput( $input, $format = NULL, $calendar = 'Jalali', $timezone = GPERSIANDATE_TIMEZONE, $fallback = '' )
	{
		if ( empty( $input ) )
			return $fallback;

		if ( is_null( $format ) )
			$format = 'Y-m-d H:i:s';

		return date( $format, self::makeFromInput( $input, $calendar, $timezone, $fallback ) );
	}

	// timezone must be UTC, since all dates stored in wp are local
	public static function monthFirstAndLast( $year, $month, $format = NULL, $calendar = 'Jalali', $timezone = 'UTC' )
	{
		if ( is_null( $format ) )
			$format = 'Y-m-d H:i:s';

		$days = self::daysInMonth( $month, $year, $calendar );

		return [
			date( $format, self::make( 0, 0, 0, $month, 1, $year, $calendar, $timezone ) ),
			date( $format, self::make( 23, 59, 59, $month, $days, $year, $calendar, $timezone ) ),
		];
	}

	// @REF: `cal_days_in_month()`
	public static function daysInMonth( $month, $year, $calendar = 'Jalali' )
	{
		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return date( 't', mktime( 0, 0, 0, $month, 1, $year ) );

		if ( 'Hijri' == $calendar )
			return intval( gPersianDateDateTime::daysInMonthHijri( $month, $year ) );

		return intval( gPersianDateDateTime::daysInMonthJalali( $month, $year ) );
	}

	// timezone must be UTC, since all dates stored in wp are local
	public static function dayOfWeek( $month, $day, $year, $calendar = 'Jalali', $timezone = 'UTC' )
	{
		return date( 'w', self::makeFromArray( [
			'year'     => $year,
			'month'    => $month,
			'day'      => $day,
			'calendar' => $calendar,
			'timezone' => $timezone,
		] ) );
	}
}
