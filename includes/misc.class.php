<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateMisc extends gPersianDateModuleCore
{


	// @source `bp_core_time_diff()`
	public static function parseMySQL( $date_string )
	{
		$parsed = NULL;

		if ( preg_match( '/^\d{4}-\d{2}-\d{2}[ ]\d{2}:\d{2}:\d{2}$/', $date_string ) ) {

			$time_chunks = explode( ':', str_replace( ' ', ':', $date_string ) );
			$date_chunks = explode( '-', str_replace( ' ', '-', $date_string ) );

			$parsed = gmmktime(
				(int) $time_chunks[1],
				(int) $time_chunks[2],
				(int) $time_chunks[3],
				(int) $date_chunks[1],
				(int) $date_chunks[2],
				(int) $date_chunks[0]
			);

		} else if ( ! is_int( $date_string ) ) {

			$parsed = 0;
		}

		return $parsed;
	}

	/**
	 * Paeses date-strings in W3C date/time formats
	 *
	 * @source `parse_w3cdtf()`
	 *
	 * @param  string $date_string
	 * @return int $timestamp
	 */
	public static function parseW3C( $date_string )
	{
		# regex to match W3C date/time formats
		$pattern = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";

		if ( preg_match( $pattern, $date_string, $match ) ) {

			list( $year, $month, $day, $hours, $minutes, $seconds ) = [ $match[1], $match[2], $match[3], $match[4], $match[5], $match[7] ];

			# calc epoch for current date assuming GMT
			$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year );

			$offset = 0;

			if ( $match[11] == 'Z' ) {

				# zulu time, aka GMT

			} else {

				list( $tz_mod, $tz_hour, $tz_min ) = [ $match[8], $match[9], $match[10] ];

				// zero out the variables
				if ( ! $tz_hour )
					$tz_hour = 0;

				if ( ! $tz_min )
					$tz_min = 0;

				$offset_secs = ( ( $tz_hour * 60 ) + $tz_min ) * 60;

				// is timezone ahead of GMT?
				// then subtract offset
				if ( $tz_mod == '+' )
					$offset_secs = $offset_secs * -1;

				$offset = $offset_secs;
			}

			$epoch = $epoch + $offset;

			return $epoch;

		} else {

			return -1;
		}
	}

	public static function calculateAge( $date, $calendar = NULL, $timezone = NULL )
	{
		$dob = gPersianDateDate::getObject( $date, $calendar, $timezone );

		if ( ! $dob )
			return FALSE;

		$now  = new \DateTime( 'now', new \DateTimeZone( gPersianDateDateTime::sanitizeTimeZone( $timezone ) ) );
		$diff = $now->diff( $dob );

		return [
			'month' => $diff->format( '%m' ),
			'day'   => $diff->format( '%d' ),
			'year'  => $diff->format( '%y' ),
		];
	}

	public static function daysTillBirthday( $month, $day, $form = 'now', $calendar = NULL )
	{
		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return gPersianDateDateTime::daysTillBirthdayGregorian( $month, $day, $form );

		else if ( 'Hijri' == $calendar )
			return gPersianDateDateTime::daysTillBirthdayHijri( $month, $day, $form );

		return gPersianDateDateTime::daysTillBirthdayJalali( $month, $day, $form );
	}
}
