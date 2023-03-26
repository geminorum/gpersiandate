<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateMisc extends gPersianDateModuleCore
{

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
