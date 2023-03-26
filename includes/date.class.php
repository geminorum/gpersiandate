<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDate extends gPersianDateModuleCore
{

	const MYSQL_FORMAT = 'Y-m-d H:i:s';
	const MYSQL_EMPTY  = '0000-00-00 00:00:00';

	// @REF: https://stackoverflow.com/a/2524710
	public static function isTimestamp( $string )
	{
		return is_numeric( $string ) && (int) $string == $string;
	}

	public static function fromObject( $format, $datetime = NULL, $timezone_string = NULL, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		if ( is_null( $datetime ) ) {

			$datetime = self::toObject( NULL, $timezone_string );

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
	public static function _fromObject( $format, $datetime = NULL, $timezone_string = NULL, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		return self::fromObject( $format, $datetime, $timezone_string, $locale, FALSE, $calendar );
	}

	// @REF: `mysql2date()`
	public static function toObject( $date = NULL, $timezone_string = NULL )
	{
		// already is an object!
		if ( is_a( $date, 'DateTime' ) || is_a( $date, 'DateTimeImmutable' ) )
			return $date;

		if ( ! $timezone_string )
			$timezone_string = gPersianDateTimeZone::current();

		if ( is_null( $date ) )
			return date_create( 'now', new \DateTimeZone( $timezone_string ) );

		if ( empty( $date ) || self::MYSQL_EMPTY === $date )
			return FALSE;

		if ( self::isTimestamp( $date ) ) {

			$datetime = date_create( '@'.$date );
			$timezone = new \DateTimeZone( $timezone_string );

			return $datetime->setTimezone( $timezone );
		}

		return date_create( $date, new \DateTimeZone( $timezone_string ) );
	}

	// FIXME: DEPRECATED
	// for back comp only
	public static function to( $format, $datetime = NULL, $timezone_string = NULL, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		self::_dev_dep( 'gPersianDateDate::fromObject()' );

		if ( FALSE === $datetime )
			return FALSE;

		if ( ! is_a( $datetime, 'DateTime' )
			&& ! is_a( $datetime, 'DateTimeImmutable' ) ) {

			$datetime = self::toObject( $datetime, $timezone_string );
		}

		return self::fromObject( $format, $datetime, $timezone_string, $locale, $translate, $calendar );
	}

	// FIXME: DROP THIS
	public static function to_OLD( $format, $time = NULL, $timezone = NULL, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
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
	public static function _to( $format, $time = NULL, $timezone = NULL, $locale = NULL, $translate = NULL, $calendar = 'Jalali' )
	{
		return self::to( $format, $time, $timezone, $locale, FALSE, $calendar );
	}

	public static function toByCalfromObject( $format, $datetime = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		return self::fromObject( $format, $datetime, NULL, NULL, $translate, $calendar );
	}

	public static function toByCal( $format, $time = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		return self::to( $format, $time, NULL, NULL, $translate, $calendar );
	}

	public static function fromObjectHijri( $format, $datetime = NULL, $timezone_string = NULL, $locale = 'ar', $translate = NULL )
	{
		return self::fromObject( $format, $datetime, $timezone_string, $locale, $translate, 'Hijri' );
	}

	public static function toHijri( $format, $time = NULL, $timezone = NULL, $locale = 'ar', $translate = NULL )
	{
		self::_dev_dep( 'gPersianDateDate::fromObjectHijri()' );

		return self::to( $format, $time, $timezone, $locale, $translate, 'Hijri' );
	}

	// @SEE: `date_parse()`
	// @REF: http://php.net/manual/en/function.getdate.php
	public static function getFromObject( $datetime = NULL, $timezone_string = NULL, $locale = NULL, $translate = FALSE, $calendar = 'Jalali' )
	{
		if ( FALSE === $datetime )
			return [];

		if ( FALSE === ( $datetime = self::toObject( $datetime, $timezone_string ) ) )
			return [];

		$array = explode( '|', self::fromObject( 's|i|G|j|w|n|Y|z|l|F', $datetime, $timezone_string, $locale, $translate, $calendar ) );

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

			// back comp only
			0 => $datetime->getTimestamp() + $datetime->getOffset(), // a sum of timestamp with timezone offset
		];
	}

	public static function get( $time = NULL, $timezone = NULL, $locale = NULL, $translate = FALSE, $calendar = 'Jalali' )
	{
		self::_dev_dep( 'gPersianDateDate::getFromObject()' );

		return call_user_func_array( [ 'gPersianDateDate', 'getFromObject' ], func_get_args() );
	}

	public static function getByCalfromObject( $datetime = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		return self::getFromObject( $datetime, NULL, GPERSIANDATE_LOCALE, $translate, $calendar );
	}

	public static function getByCal( $time = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		self::_dev_dep();

		return self::get( $time, NULL, GPERSIANDATE_LOCALE, $translate, $calendar );
	}

	public static function getByPost( $post = NULL, $calendar = 'Jalali', $translate = FALSE )
	{
		if ( FALSE === ( $datetime = gPersianDateWordPress::getPostDatetime( $post ) ) )
			return FALSE;

		return self::getFromObject( $datetime, NULL, GPERSIANDATE_LOCALE, $translate, $calendar );
	}

	public static function makeObject( $hour, $minute, $second, $month, $day, $year, $calendar = 'Jalali', $timezone = NULL )
	{
		return call_user_func_array( [ 'gPersianDateDateTime', 'makeObject' ], func_get_args() );
	}

	public static function make( $hour, $minute, $second, $month, $day, $year, $calendar = 'Jalali', $timezone = NULL )
	{
		self::_dev_dep( 'gPersianDateDate::makeObject()' );

		return call_user_func_array( [ 'gPersianDateDateTime', 'make' ], func_get_args() );
	}

	public static function makeMySQL( $hour, $minute, $second, $month, $day, $year, $calendar = 'Jalali', $timezone = NULL )
	{
		$datetime = call_user_func_array( [ 'gPersianDateDateTime', 'make' ], func_get_args() );

		return $datetime
			? $datetime->format( self::MYSQL_FORMAT )
			: self::MYSQL_EMPTY;
	}

	public static function makeObjectFromArray( $array = [] )
	{
		$parts = self::atts( [
			'year'     => 1362, // ;)
			'month'    => 1,
			'day'      => 1,
			'hour'     => 0,
			'minute'   => 0,
			'second'   => 0,
			'calendar' => 'Jalali',
			'timezone' => NULL,
		], $array );

		return self::makeObject(
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

	public static function makeFromArray( $array = [] )
	{
		self::_dev_dep( 'gPersianDateDate::makeObjectFromArray()' );

		$parts = self::atts( [
			'year'     => 1362, // ;)
			'month'    => 1,
			'day'      => 1,
			'hour'     => 0,
			'minute'   => 0,
			'second'   => 0,
			'calendar' => 'Jalali',
			'timezone' => NULL,
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
			$format = self::MYSQL_FORMAT;

		$datetime = self::makeObjectFromArray( $array );

		return $datetime
			? $datetime->format( $format )
			: self::MYSQL_EMPTY;
	}

	// FIXME: DROP THIS
	public static function makeObjectFromInput_OLD( $input, $calendar = 'Jalali', $timezone = NULL, $fallback = '' )
	{
		if ( empty( $input ) )
			return $fallback;

		// FIXME: needs sanity checks
		$parts = explode( '/', str_replace( [ '-', '\\' ], '/', apply_filters( 'string_format_i18n_back', $input ) ) );

		return self::makeObject( 0, 0, 0, $parts[1], $parts[2], $parts[0], $calendar, $timezone );
	}

	// '1393-4-12 13:34:26'
	// '1393/4/12 13:34:26'
	public static function makeObjectFromInput( $input, $calendar = 'Jalali', $timezone = NULL, $fallback = '' )
	{
		if ( empty( $input ) )
			return $fallback;

		$string   = apply_filters( 'string_format_i18n_back', $input );
		$currents = self::getFromObject( NULL, $timezone, NULL, FALSE, $calendar );

		preg_match_all( '/\d+/', $string, $matches );

		$parts = self::atts( [
			0 => $currents['year'],
			1 => $currents['mon'],
			2 => $currents['mday'],
			3 => 0, // $currents['hours'],
			4 => 0, // $currents['minutes'],
			5 => 0, // $currents['seconds'],
		], $matches[0] );

		return self::makeObject( $parts[3], $parts[4], $parts[5], $parts[1], $parts[2], $parts[0], $calendar, $timezone );
	}

	public static function makeFromInput( $input, $calendar = 'Jalali', $timezone = NULL, $fallback = '' )
	{
		self::_dev_dep( 'gPersianDateDate::makeObjectFromInput()' );

		if ( empty( $input ) )
			return $fallback;

		$string   = apply_filters( 'string_format_i18n_back', $input );
		$currents = self::getFromObject( NULL, $timezone, NULL, FALSE, $calendar );

		preg_match_all( '/\d+/', $string, $matches );

		$parts = self::atts( [
			0 => $currents['year'],
			1 => $currents['mon'],
			2 => $currents['mday'],
			3 => 0, // $currents['hours'],
			4 => 0, // $currents['minutes'],
			5 => 0, // $currents['seconds'],
		], $matches[0] );

		return self::make( $parts[3], $parts[4], $parts[5], $parts[1], $parts[2], $parts[0], $calendar, $timezone );
	}

	public static function makeMySQLFromInput( $input, $format = NULL, $calendar = 'Jalali', $timezone = NULL, $fallback = '' )
	{
		if ( empty( $input ) )
			return $fallback;

		$datetime = self::makeObjectFromInput( $input, $calendar, $timezone, $fallback );

		return $datetime
			? $datetime->format( $format ?: self::MYSQL_FORMAT )
			: self::MYSQL_EMPTY;
	}

	// timezone must be UTC, since all dates stored in wp are local
	public static function monthFirstAndLast( $year, $month, $format = NULL, $calendar = 'Jalali', $timezone = 'UTC' )
	{
		$days  = self::daysInMonth( $month, $year, $calendar );
		$first = self::makeObject( 0, 0, 0, $month, 1, $year, $calendar, $timezone );
		$last  = self::makeObject( 23, 59, 59, $month, $days, $year, $calendar, $timezone );

		return [
			$first ? $first->format( $format ?: self::MYSQL_FORMAT ) : self::MYSQL_EMPTY,
			$last  ? $last->format( $format ?: self::MYSQL_FORMAT )  : self::MYSQL_EMPTY,
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

	// NOTE: timezone must be UTC, since all dates stored in wp are local
	public static function dayOfWeek( $month, $day, $year, $calendar = 'Jalali', $timezone = 'UTC' )
	{
		$datetime = self::makeObjectFromArray( [
			'year'     => $year,
			'month'    => $month,
			'day'      => $day,
			'calendar' => $calendar,
			'timezone' => $timezone,
		] );

		if ( FALSE === $datetime )
			return 0;

		return $datetime->format( 'w' );
	}

	public static function dayOfYear( $month, $day, $calendar = NULL )
	{
		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return gPersianDateDateTime::dayOfYearGregorian( $month, $day );

		else if ( 'Hijri' == $calendar )
			return gPersianDateDateTime::dayOfYearHijri( $month, $day );

		return gPersianDateDateTime::dayOfYearJalali( $month, $day );
	}

	public static function isLeapYear( $year, $calendar = NULL )
	{
		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return gPersianDateDateTime::isLeapYearGregorian( $year );

		else if ( 'Hijri' == $calendar )
			return gPersianDateDateTime::isLeapYearHijri( $year );

		return gPersianDateDateTime::isLeapYearJalali( $year );
	}

	public static function check( $month, $day, $year, $calendar = NULL )
	{
		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return checkdate( $month, $day, $year );

		else if ( 'Hijri' == $calendar )
			return gPersianDateDateTime::checkHijri( $month, $day, $year );

		return gPersianDateDateTime::checkJalali( $month, $day, $year );
	}

	// @REF: https://schoolsofweb.com/how-to-compare-two-dates-in-php/
	public static function compare( $first, $second, $calendar = NULL, $timezone = NULL )
	{
		$first_date  = self::getObject( $first, $calendar, $timezone, FALSE );
		$second_date = self::getObject( $second, $calendar, $timezone, FALSE );
		$date_diff   = $first_date->diff( $second_date )->format( '%R%a' );

		return 0 === $date_diff;
	}

	// tries to parse and make an object
	public static function getObject( $date, $calendar = NULL, $timezone = NULL )
	{
		if ( ! $date )
			return FALSE;

		if ( is_a( $date, 'DateTime' ) || is_a( $date, 'DateTimeImmutable' ) )
			return $date;

		if ( is_string( $date ) )
			return self::makeObjectFromInput( $date, $calendar, $timezone, FALSE );

		if ( ! is_array( $date ) )
			return FALSE;

		// NOTE: diffrent from `makeObjectFromArray()`
		$parts = self::atts( [
			'year'     => 0,
			'month'    => 0,
			'day'      => 0,
			'hour'     => 0,
			'minute'   => 0,
			'second'   => 0,
			'calendar' => $calendar,
			'timezone' => $timezone,
		], $date );

		if ( empty( $parts['month'] ) || empty( $parts['day'] ) || empty( $parts['year'] ) )
			return FALSE;

		return self::makeObject(
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
}
