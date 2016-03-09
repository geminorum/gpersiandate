<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDateTime extends gPersianDateModuleCore
{

	// SEE: http://www.paulund.co.uk/datetime-php
	public static function to( $time, $format, $timezone = NULL, $calendar = NULL )
	{
		$result       = '';
		$datetimezone = new DateTimeZone( $timezone );

		if ( is_a( $time, 'DateTime' ) || is_numeric( $time ) ) {
			$datetime = new DateTime( NULL, $datetimezone );
			$datetime->setTimestamp( $time );
		} else if ( is_string( $time ) ) {
			$datetime = new DateTime( $time, $datetimezone );
		}

		if ( 'Gregorian' == $calendar )
			return $datetime->format( $format );

		else if ( 'Hijri' == $calendar )
			$convertor = array( 'self', 'toHijri' );

		else
			$convertor = array( 'self', 'toJalali' );

		list( $year,  $month,  $day  ) = explode( '-', $datetime->format( 'Y-n-j' ) );
		list( $jyear, $jmonth, $jday ) = call_user_func_array( $convertor, array( $year, $month, $day ) );

		for ( $i = 0; $i < strlen( $format ); $i++ ) {

			switch ( $format[$i] ) {

				// A two digit representation of a year (Examples: 99 or 03)
				case 'y':
					$result .= substr( $jyear, 2, 4 );
				break;

				// A full numeric representation of a year, 4 digits (Examples: 1999 or 2003)
				case 'Y':
					$result .= $jyear;
				break;

				// There is no short textual representation of months in persian so we use full textual representaions instead.
				case 'M':

				// A full textual representation of a month (Farvardin through Esfand)
				case 'F':
					// $result .= $this->translate( self::$j_months[$jmonth - 1] );
					$result .= gPersianDateStrings::month( $jmonth, FALSE, $calendar );
				break;

				// Numeric representation of a month, with leading zeros (01 through 12)
				case 'm':
					// $result .= sprintf('%02d', $jmonth);
					$result .= zeroise( $jmonth, 2 );
				break;

				// Numeric representation of a month, without leading zeros (1 through 12)
				case 'n':
					$result .= $jmonth;
				break;

				// Day of the month, 2 digits with leading zeros (01 to 31)
				case 'd':
					// $result .= sprintf('%02d', $jday);
					$result .= zeroise( $jday, 2 );
				break;

				// FIXME: check this!
				// A textual representation of a day, three letters (Mon through Sun)
				case 'D':

				// FIXME: check this!
				// A full textual representation of the day of the week (Sunday through Saturday)
				case 'l':
					$result .= gPersianDateStrings::dayoftheweek( ( $datetime->format( 'w' ) + 1 ) % 7, FALSE, $calendar );
				break;

				// Day of the month without leading zeros (1 to 31)
				case 'j':
					$result .= $jday;
				break;

				// Numeric representation of the day of the week (0 (for Saturday) through 6 (for Friday))
				case 'w':
					// $result .= (parent::format("w") + 1) % 7;
					$result .= ( $datetime->format( 'w' ) + 1 ) % 7;
				break;

				// Number of days in the given month (29 through 31)
				case 't':
					if ( $jmonth < 12 )
						$result .= self::$j_days_in_month[$jmonth-1];
					else if ( self::checkJalali( $jmonth, 30, $jyear ) )
						$result .= '30';
					else
						$result .= '29';
				break;

				// The day of the year starting from 0 (0 through 365)
				case 'z':
					$day_of_year = 0;
					for ( $n=0; $n<$jmonth-1; $n++ )
						$day_of_year += self::$j_days_in_month[$n];
					$day_of_year += $jday-1;
					$result .= $day_of_year;
				break;

				// Whether it's a leap year (1 if it is a leap year, 0 otherwise.)
				case 'L':
					$result .= self::checkJalali( 12, 30, $jyear ) ? '1' : '0';
				break;

				// Week number of year, weeks starting on Saturday
				case 'W':
					$z = $datetime->format( 'z' );
					$firstSaturday = ( $z - $datetime->format( 'w' ) + 7 ) % 7;
					$days = $z - $firstSaturday; // Number of days after the first Saturday of the year
					if ( $days < 0 ) {
						$z += self::checkJalali( 12, 30, $jyear-1 ) ? 366 : 365;
						$firstSaturday = ( $z - $datetime->format( 'w' ) + 7 ) % 7;
						$days = $z - $firstSaturday;
					}
					$result .= floor( $days / 7 ) + 1;
				break;

				case 'a': // Lowercase Ante meridiem and Post meridiem (am or pm)
				case 'A': // Uppercase Ante meridiem and Post meridiem (AM or PM)
					$result .= gPersianDateStrings::meridiemAntePost( $datetime->format( $format[$i] ), FALSE, $calendar );
				break;

				// case "S": //English ordinal suffix for the day of the month, 2 characters (st, nd, rd or th. Works well with j)

				case "\\":
					if ( $i+1 < strlen( $format ) )
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

	static public function checkJalali( $j_m, $j_d, $j_y )
	{
		if ( $j_y < 0 || $j_y > 32767 || $j_m < 1 || $j_m > 12 || $j_d < 1 || $j_d >
			(self::$j_days_in_month[$j_m-1] + ($j_m == 12 && !(($j_y-979)%33%4))))
				return FALSE;

		return TRUE;
	}

	private static $g_days_in_month = array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
	private static $j_days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
	private static $j_months = array( 'Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad', 'Shahrivar', 'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand' );

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

		return array( $jy, $jm, $jd );
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

		return array( $gy, $gm, $gd );
	}

	// Division
	private static function div( $a, $b )
	{
		return (int) ( $a / $b );
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

		return array( $year, $month, $day );
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

		return array( $year, $month, $day );
	}
}
