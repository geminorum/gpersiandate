<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDateTime extends gPersianDateModuleCore
{

	public static function to( $time, $format, $timezone = NULL, $calendar = NULL )
	{
		$result   = '';
		$calendar = self::sanitizeCalendar( $calendar );

		if ( is_numeric( $time ) ) {

			$timezone = self::sanitizeTimeZone( $timezone );
			$datetime = new \DateTime( NULL, new \DateTimeZone( $timezone ) );
			$datetime->setTimestamp( $time );

		} else if ( is_string( $time ) ) {

			$timezone = self::sanitizeTimeZone( $timezone );
			$datetime = date_create( $time, new \DateTimeZone( $timezone ) );

			if ( FALSE === $datetime )
				return '';

		} else if ( is_a( $time, 'DateTime' ) || is_a( $time, 'DateTimeImmutable' ) ) {

			$datetime = $time;
		}

		if ( 'Gregorian' == $calendar )
			return $datetime->format( $format );

		else if ( 'Hijri' == $calendar )
			$convertor = [ 'self', 'toHijri' ];

		else
			$convertor = [ 'self', 'toJalali' ];

		$year  = $datetime->format( 'Y' );
		$month = $datetime->format( 'n' );
		$day   = $datetime->format( 'j' );

		list( $jyear, $jmonth, $jday ) = call_user_func_array( $convertor, [ $year, $month, $day ] );

		for ( $i = 0; $i < strlen( $format ); $i++ ) {

			switch ( $format[$i] ) {

				case 'y': // A two digit representation of a year (Examples: 99 or 03)

					$result .= substr( $jyear, 2, 4 );

				break;

				case 'Y': // A full numeric representation of a year, 4 digits (Examples: 1999 or 2003)

					$result .= $jyear;

				break;

				case 'M': // There is no short textual representation of months in persian so we use full textual representaions instead.
				case 'F': // A full textual representation of a month (Farvardin through Esfand)

					$result .= gPersianDateStrings::month( $jmonth, FALSE, $calendar );

				break;

				case 'm': // Numeric representation of a month, with leading zeros (01 through 12)

					$result .= sprintf( '%02d', $jmonth );

				break;

				case 'n': // Numeric representation of a month, without leading zeros (1 through 12)

					$result .= $jmonth;

				break;

				case 'd': // Day of the month, 2 digits with leading zeros (01 to 31)

					$result .= sprintf( '%02d', $jday );

				break;

				// FIXME: check this!
				case 'D': // A textual representation of a day, three letters (Mon through Sun)
				case 'l': // A full textual representation of the day of the week (Sunday through Saturday)

					$result .= gPersianDateStrings::dayoftheweek(
						$datetime->format( 'w' ) % 7, FALSE, $calendar );

				break;

				case 'j': // Day of the month without leading zeros (1 to 31)

					$result .= $jday;

				break;

				case 'w': // Numeric representation of the day of the week (0 (for Saturday) through 6 (for Friday))

					$result .= ( $datetime->format( 'w' ) + 1 ) % 7;

				break;

				case 't': // Number of days in the given month (29 through 31)

					if ( 'Hijri' == $calendar )
						$result .= self::daysInMonthHijri( $jmonth, $jyear );

					else
						$result .= self::daysInMonthJalali( $jmonth, $jyear );

				break;

				case 'z': // The day of the year starting from 0 (0 through 365)

					if ( 'Hijri' == $calendar )
						$result .= self::dayOfYearHijri( $jmonth, $jday ) - 1;

					else
						$result .= self::dayOfYearJalali( $jmonth, $jday ) - 1;

				break;

				case 'L': // Whether it's a leap year (1 if it is a leap year, 0 otherwise.)

					if ( 'Hijri' == $calendar )
						$result .= self::isLeapYearJalali( $jyear ) ? '1' : '0';

					else
						$result .= self::isLeapYearHijri( $jyear ) ? '1' : '0';

				break;

				case 'W': // Week number of year, weeks starting on Saturday

					$z = $datetime->format( 'z' );
					$firstSaturday = ( $z - $datetime->format( 'w' ) + 7 ) % 7;
					$days = $z - $firstSaturday; // Number of days after the first Saturday of the year

					if ( $days < 0 ) {
						$z += self::checkJalali( 12, 30, $jyear - 1 ) ? 366 : 365;
						$firstSaturday = ( $z - $datetime->format( 'w' ) + 7 ) % 7;
						$days = $z - $firstSaturday;
					}

					$result .= floor( $days / 7 ) + 1;

				break;

				case 'a': // Lowercase Ante meridiem and Post meridiem (am or pm)
				case 'A': // Uppercase Ante meridiem and Post meridiem (AM or PM)

					$result .= gPersianDateStrings::meridiemAntePost(
						$datetime->format( $format[$i] ), FALSE, $calendar );

				break;

				// case "S": //English ordinal suffix for the day of the month, 2 characters (st, nd, rd or th. Works well with j)

				case "\\":

					if ( $i + 1 < strlen( $format ) )
						$result .= $format[++$i];
					else
						$result .= $format[$i];

				break;

				default:
					$result .= $datetime->format( $format[$i] );
			}
		}

		return $result;
	}

	public static function sanitizeTimeZone( $timezone )
	{
		if ( is_null( $timezone ) && defined( 'GPERSIANDATE_TIMEZONE' ) )
			return GPERSIANDATE_TIMEZONE;

		if ( is_numeric( $timezone ) )
			return gPersianDateTimeZone::fromOffset( $timezone );

		if ( is_string( $timezone ) )
			return $timezone;

		return date_default_timezone_get();
	}

	public static function sanitizeCalendar( $calendar )
	{
		if ( ! $calendar )
			return 'Jalali';

		if ( in_array( $calendar, [ 'Jalali', 'jalali', 'Persian', 'persian' ] ) )
			return 'Jalali';

		if ( in_array( $calendar, [ 'Hijri', 'hijri', 'Islamic', 'islamic' ] ) )
			return 'Hijri';

		if ( in_array( $calendar, [ 'Gregorian', 'gregorian' ] ) )
			return 'Gregorian';

		return 'Jalali';
	}

	public static function todayGregorian( $time = 'now', $timezone = NULL )
	{
		$timezone = self::sanitizeTimeZone( $timezone );
		$datetime = new \DateTime( $time, new \DateTimeZone( $timezone ) );

		return explode( '-', $datetime->format( 'Y-n-j' ) );
	}

	public static function todayJalali( $time = 'now', $timezone = NULL )
	{
		$timezone = self::sanitizeTimeZone( $timezone );
		$datetime = new \DateTime( $time, new \DateTimeZone( $timezone ) );

		return call_user_func_array( [ __CLASS__, 'toJalali' ], explode( '-', $datetime->format( 'Y-n-j' ) ) );
	}

	public static function todayHijri( $time = 'now', $timezone = NULL )
	{
		$timezone = self::sanitizeTimeZone( $timezone );
		$datetime = new \DateTime( $time, new \DateTimeZone( $timezone ) );

		return call_user_func_array( [ __CLASS__, 'toHijri' ], explode( '-', $datetime->format( 'Y-n-j' ) ) );
	}

	// @SOURCE: https://davidwalsh.name/php-function-calculating-days-in-a-month
	// @REF: `cal_days_in_month()`
	public static function daysInMonthGregorian( $month, $year )
	{
		return $month == 2 ? ( $year % 4 ? 28 : ( $year % 100 ? 29 : ( $year %400 ? 28 : 29 ) ) ) : ( ( $month - 1 ) % 7 % 2 ? 30 : 31 );
	}

	// @REF: `cal_days_in_month()`
	public static function daysInMonthGregorian_ALT( $month, $year )
	{
		// RECOMMANDED:
		// return date( 't', mktime( 0, 0, 0, $month, 1, $year ) );

		if ( $month == 2 )
			return self::isLeapYearGregorian( $year ) ? 29 : 28;

		return self::$g_days_in_month[$month-1];
	}

	// @REF: `cal_days_in_month()`
	public static function daysInMonthJalali( $month, $year )
	{
		if ( $month < 12 )
			return self::$j_days_in_month[$month-1];

		return self::isLeapYearJalali( $year ) ? 30 : 29;
	}

	// FIXME
	public static function daysInMonthHijri( $month, $year )
	{
		return self::daysInMonthJalali( $month, $year );
	}

	// @SOURCE: http://php.net/manual/en/function.cal-days-in-month.php#102855
	public static function daysTillBirthdayGregorian( $month, $day, $now = 'now' )
	{
		if ( 'now' == $now )
			$now = mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) );

		$year = date( 'Y', $now );
		$next = mktime( 0, 0, 0, $month, $day, $year );
		$days = self::isLeapYearGregorian( $year ) ? 366 : 365;

		if ( $next < $now )
			$next = $next + ( 60 * 60 * 24 * $days );

		return intval( ( $next - $now ) / ( 60 * 60 * 24 ) );
	}

	public static function daysTillBirthdayJalali( $month, $day, $form = 'now' )
	{
		list( $this_year, $this_month, $this_day ) = self::todayJalali( $form );

		$days = self::isLeapYearJalali( $this_year ) ? 366 : 365;
		$next = self::makeJalali( 0, 0, 0, $month, $day, $this_year + 1 );
		$now  = self::makeJalali( 0, 0, 0, $this_month, $this_day, $this_year );

		if ( $next < $now )
			$next = $next + ( 60 * 60 * 24 * $days );

		return intval( ( $next - $now ) / ( 60 * 60 * 24 ) );
	}

	// The day of the year: 1 through 366
	public static function dayOfYearJalali( $month, $day )
	{
		$day_of_year = 0;

		for ( $n = 0; $n < $month - 1; $n++ )
			$day_of_year += self::$j_days_in_month[$n];

		return $day_of_year + $day;
	}

	// FIXME
	public static function dayOfYearHijri( $month, $day )
	{
		return self::dayOfYearJalali( $month, $day );
	}

	public static function make( $hour, $minute, $second, $jmonth, $jday, $jyear, $calendar = 'Jalali', $timezone = NULL )
	{
		$calendar = self::sanitizeCalendar( $calendar );
		$timezone = self::sanitizeTimeZone( $timezone );

		if ( 'Gregorian' == $calendar )
			list( $year, $month, $day ) = [ $jyear, $jmonth, $jday ];

		else if ( 'Hijri' == $calendar )
			list( $year, $month, $day ) = self::fromHijri( $jyear, $jmonth, $jday );

		else
			list( $year, $month, $day ) = self::fromJalali( $jyear, $jmonth, $jday );

		$time  = $year.'-'.sprintf( '%02d', $month ).'-'.sprintf( '%02d', $day ).' ';
		$time .= sprintf( '%02d', $hour ).':'.sprintf( '%02d', $minute ).':'.sprintf( '%02d', $second );

		try {

			$datetime = new \DateTime( $time, new \DateTimeZone( $timezone ) );
			return $datetime->format( 'U' );

		} catch ( \Exception $e ) {

			// echo $e->getMessage();
			return FALSE;
		}
	}

	// @SOURCE: https://davidwalsh.name/checking-for-leap-year-using-php
	public static function isLeapYearGregorian( $year )
	{
		return ( ( ( $year % 4 ) == 0 ) && ( ( ( $year % 100 ) != 0 ) || ( ( $year % 400 ) == 0 ) ) );
	}

	// @SOURCE: https://gitlab.com/Iranium/Iranium/
	// @REF: https://goo.gl/wZCU76
	public function isLeapYearJalali( $year )
	{
		$a = 0.025;
		$b = 266;

		if ( $year <= 0 )
			return false;

		$c = ( ( $year + 38 ) % 2820 ) * 0.24219 + $a; // 0.24219 ~ Extra days of a year
		$d = ( ( $year + 39 ) % 2820 ) * 0.24219 + $a; // 38 days is the difference of epoch to 2820-year circle.

		$frac_c = intval( ( $c - intval( $c ) ) * 1000 );
		$frac_d = intval( ( $d - intval( $d ) ) * 1000 );

		return ( $frac_c <= $b && $frac_d > $b );
	}

	public static function isLeapYearJalali_OLD( $year )
	{
		return self::checkJalali( 12, 30, $year );
	}

	// @REF: https://jdf.scr.ir/tarikh/?t=tahvile_sal#jn3
	public static function isLeapYearJalali_Alt( $year )
	{
		return ( ( ( $year % 33 ) % 4 ) - 1 ) == ( (int) ( ( $year % 33 ) * 0.05 ) );
	}

	// FIXME
	public static function isLeapYearHijri( $year )
	{
		return FALSE;
	}

	public static function check( $month, $day, $year, $calendar = 'Jalali' )
	{
		$calendar = self::sanitizeCalendar( $calendar );

		if ( 'Gregorian' == $calendar )
			return checkdate( $month, $day, $year );

		else if ( 'Hijri' == $calendar )
			return self::checkHijri( $month, $day, $year );

		return self::checkJalali( $month, $day, $year );
	}

	public static function checkJalali( $month, $day, $year )
	{
		if ( $year < 0 || $year > 32767 )
			return FALSE;

		if ( $month < 1 || $month > 12 )
			return FALSE;

		if ( $day < 1 || $day > ( self::$j_days_in_month[$month-1] + ( $month == 12 && ! ( ( $year - 979 ) % 33 % 4 ) ) ) )
			return FALSE;

		return TRUE;
	}

	// FIXME
	public static function checkHijri( $month, $day, $year )
	{
		return TRUE;
	}

	private static $g_days_in_month = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
	private static $j_days_in_month = [ 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 ];

	// Gregorian to Jalali Conversion
	// Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
	public static function toJalali( $g_y, $g_m, $g_d )
	{
		$gy = $g_y-1600;
		$gm = $g_m-1;
		$gd = $g_d-1;

		$g_day_no = 365*$gy+self::div($gy+3, 4)-self::div($gy+99, 100)+self::div($gy+399, 400);

		for ($i=0; $i < $gm; ++$i)
			$g_day_no += self::$g_days_in_month[$i];

		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
			$g_day_no++;

		$g_day_no += $gd;

		$j_day_no = $g_day_no-79;

		$j_np = self::div($j_day_no, 12053);
		$j_day_no = $j_day_no % 12053;

		$jy = 979+33*$j_np+4*self::div($j_day_no, 1461);

		$j_day_no %= 1461;

		if ($j_day_no >= 366) {
			$jy += self::div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		}

		for ($i = 0; $i < 11 && $j_day_no >= self::$j_days_in_month[$i]; ++$i)
			$j_day_no -= self::$j_days_in_month[$i];

		$jm = $i+1;
		$jd = $j_day_no+1;

		return [ $jy, $jm, $jd ];
	}

	// Back Comp
	public static function toGregorian( $j_y, $j_m, $j_d )
	{
		return self::fromJalali( $j_y, $j_m, $j_d );
	}

	// Jalali to Gregorian Conversion
	// Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
	public static function fromJalali( $j_y, $j_m, $j_d )
	{
		$jy = $j_y - 979;
		$jm = $j_m - 1;
		$jd = $j_d - 1;

		$j_day_no = 365 * $jy + self::div( $jy, 33 ) * 8 + self::div( $jy % 33 + 3, 4 );

		for ( $i = 0; $i < $jm; ++$i )
			$j_day_no += self::$j_days_in_month[$i];

		$j_day_no += $jd;

		$g_day_no = $j_day_no + 79;

		$gy = 1600 + 400 * self::div( $g_day_no, 146097 );
		$g_day_no = $g_day_no % 146097;

		$leap = TRUE;

		if ( $g_day_no >= 36525 ) {
			$g_day_no--;
			$gy += 100 * self::div( $g_day_no, 36524 );
			$g_day_no = $g_day_no % 36524;

			if ( $g_day_no >= 365 )
				$g_day_no++;
			else
				$leap = FALSE;
		}

		$gy += 4 * self::div( $g_day_no, 1461 );
		$g_day_no %= 1461;

		if ( $g_day_no >= 366 ) {
			$leap = FALSE;

			$g_day_no--;
			$gy += self::div( $g_day_no, 365 );
			$g_day_no = $g_day_no % 365;
		}

		for ( $i = 0; $g_day_no >= self::$g_days_in_month[$i] + ( $i == 1 && $leap ); $i++ )
			$g_day_no -= self::$g_days_in_month[$i] + ( $i == 1 && $leap );

		$gm = $i + 1;
		$gd = $g_day_no + 1;

		return [ $gy, $gm, $gd ];
	}

	// Division
	private static function div( $a, $b )
	{
		return (int) ( $a / $b );
	}

	/**
	 * Author: JDF.SCR.IR =>> Download Full Version : http://jdf.scr.ir/jdf
	 * License: GNU/LGPL _ Open Source & Free _ Version: 2.70 : [2017=1395]
	 *
	 * 1461 = 365*4 + 4/4   &  146097 = 365*400 + 400/4 - 400/100 + 400/400
	 * 12053 = 365*33 + 32/4    &    36524 = 365*100 + 100/4 - 100/100
	*/
	public static function toJalali_Alt( $gy, $gm, $gd )
	{
		$g_d_m = [ 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 ];

		if ( $gy > 1600 ){
			$jy = 979;
			$gy-= 1600;
		} else {
			$jy = 0;
			$gy-= 621;
		}

		$gy2 = $gm > 2 ? $gy + 1 : $gy;

		$days = ( 365 * $gy )
			+ ( (int) ( ( $gy2 + 3 ) / 4 ) )
			- ( (int) ( ( $gy2 + 99 ) / 100 ) )
			+ ( (int) ( ( $gy2 + 399 ) / 400 ) )
			- 80 + $gd + $g_d_m[$gm - 1];

		$jy+= 33 * ( (int) ( $days / 12053 ) );
		$days%= 12053;
		$jy+= 4 * ( (int) ( $days / 1461 ) );
		$days%= 1461;
		$jy+= (int) ( ( $days - 1 ) / 365 );

		if ( $days > 365 )
			$days = ( $days - 1 ) % 365;

		if ( $days < 186 ) {
			$jm = 1 + (int) ( $days / 31 );
			$jd = 1 + ( $days % 31 );
		} else {
			$jm = 7 + (int) ( ( $days - 186 ) / 30 );
			$jd = 1 + ( ( $days - 186 ) % 30 );
		}

		return [ $jy, $jm, $jd ];
	}

	/**
	 * Author: JDF.SCR.IR =>> Download Full Version : http://jdf.scr.ir/jdf
	 * License: GNU/LGPL _ Open Source & Free _ Version: 2.70 : [2017=1395]
	*/
	public static function fromJalali_Alt( $jy, $jm, $jd )
	{
		if ( $jy > 979 ) {
			$gy = 1600;
			$jy-= 979;
		} else {
			$gy = 621;
		}

		$days = ( 365 * $jy ) + ( ( (int) ( $jy / 33 ) ) * 8 ) + ( (int) ( ( ( $jy % 33 ) + 3 ) / 4 ) ) + 78 + $jd + ( ( $jm < 7 ) ? ( $jm - 1 ) * 31 : ( ( $jm - 7 ) * 30 ) + 186 );
		$gy+= 400 * ( (int) ( $days / 146097 ) );
		$days%= 146097;

		if ( $days > 36524 ) {
			$gy+= 100 * ( (int) ( --$days / 36524 ) );
			$days%= 36524;

			if ( $days >= 365 )
				$days++;
		}

		$gy+= 4 * ( (int) ( ( $days ) / 1461 ) );
		$days%= 1461;
		$gy+= (int) ( ( $days - 1) / 365 );

		if ( $days > 365 )
			$days = ( $days - 1 ) % 365;

		$gd = $days + 1;

		foreach ( [ 0, 31, ( ( ( $gy % 4 == 0 ) && ( $gy % 100 != 0 ) ) || ( $gy % 400 == 0 ) ) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ] as $gm => $v ) {

			if ( $gd <= $v )
				break;

			$gd-= $v;
		}

		return [ $gy, $gm, $gd ];
	}

	public static function toHijri( $year, $month, $day )
	{
		if ( $year > 1582 or ( $year==1581 and $month > 9 and $day > 14 ) ){
			$int1=(int)(($month-14)/12);
			$jd=(int)((1461*($year+4800+$int1))/4)+(int)((367*($month-2-(12*($int1))))/12)-(int)((3*((int)(($year+4900+$int1)/100)))/4)+$day-32075;
		} else {
			$jd=(367*$year)-(int)((7*($year+5001+(int)(($month-9)/7)))/4)+(int)((275*$month)/9)+$day+1729777;
		}

		$l=$jd-1948440+10632;
		$n=(int)(($l-1)/10631);
		$l=$l-10631*$n+354;
		$j=(((int)((10985-$l)/5316))*((int)((50*$l)/17719)))+(((int)($l/5670))*((int)((43*$l)/15238)));
		$l=$l-((int)((30-$j)/15))*((int)((17719*$j)/50))-((int)($j/16))*((int)((15238*$j)/43))+29;
		$month=(int)((24*$l)/709);
		$day=$l-(int)((709*$month)/24);
		$year=(30*$n)+$j-30;

		return [ $year, $month, $day ];
	}

	public static function fromHijri( $year, $month, $day )
	{
		$jd=(int)(((11*$year)+3)/30)+(354*$year)+(30*$month)-(int)(($month-1)/2)+$day+1948440-385;

		if ( $jd > 2299160 ) {
			$l=$jd+68569;
			$n=(int)((4*$l)/146097);
			$l=$l-(int)((146097*$n+3)/4);
			$i=(int)((4000*($l+1))/1461001);
			$l=$l-(int)((1461*$i)/4)+31;
			$j=(int)((80*$l)/2447);
			$day=$l-(int)((2447*$j)/80);
			$l=(int)($j/11);
			$month=$j+2-(12*$l);
			$year=(100*($n-49))+$i+$l;
		} else {
			$j=$jd+1402;
			$k=(int)(($j-1)/1461);
			$l=$j-(1461*$k);
			$n=(int)(($l-1)/365)-(int)($l/1461);
			$i=$l-(365*$n)+30;
			$j=(int)((80*$i)/2447);
			$day=$i-(int)((2447*$j)/80);
			$i=(int)($j/11);
			$month=$j+2-(12*$i);
			$year=(4*$k)+$n+$i-4716;
		}

		return [ $year, $month, $day ];
	}
}
