<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateStrings extends gPersianDateModuleCore
{
	// OLD: get_month()
	// http://www.wikiwand.com/en/Month
	public static function month( $month, $all = FALSE, $calendar = NULL )
	{
		if ( is_null( $calendar ) )
			$calendar = 'Jalali';

		switch( $calendar ) {

			case 'Gregorian' :

				// FIXME: !!
				$months = array(
					'01' => __( 'January', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'02' => __( 'February', GPERSIANDATE_TEXTDOMAIN ), // 28 days, 29 in leap years
					'03' => __( 'March', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'04' => __( 'April', GPERSIANDATE_TEXTDOMAIN ), // 30 days
					'05' => __( 'May', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'06' => __( 'June', GPERSIANDATE_TEXTDOMAIN ), // 30 days
					'07' => __( 'July', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'08' => __( 'August', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'09' => __( 'September', GPERSIANDATE_TEXTDOMAIN ), // 30 days
					'10' => __( 'October', GPERSIANDATE_TEXTDOMAIN ), // 31 days
					'11' => __( 'November', GPERSIANDATE_TEXTDOMAIN ), // 30 days
					'12' => __( 'December', GPERSIANDATE_TEXTDOMAIN ), // 31 days
				);

			break;
			case 'Hijri' :

				$months = array(
					'01' => __( 'Muharram', GPERSIANDATE_TEXTDOMAIN ), // (Restricted/sacred) محرّم
					'02' => __( 'Safar', GPERSIANDATE_TEXTDOMAIN ), // (Empty/Yellow) صفر
					'03' => __( 'Rabi I', GPERSIANDATE_TEXTDOMAIN ), // Rabī' al-Awwal/Rabi' I (First Spring) ربيع الأول
					'04' => __( 'Rabi II', GPERSIANDATE_TEXTDOMAIN ), // Rabī’ ath-Thānī/Rabi` al-Aakhir/Rabi' II (Second spring or Last spring) ربيع الآخر أو ربيع الثاني
					'05' => __( 'Jumada I', GPERSIANDATE_TEXTDOMAIN ), // Jumada al-Awwal/Jumaada I (First Freeze) جمادى الأول
					'06' => __( 'Jumada II', GPERSIANDATE_TEXTDOMAIN ), // Jumada ath-Thānī or Jumādā al-Thānī/Jumādā II (Second Freeze or Last Freeze) جمادى الآخر أو جمادى الثاني
					'07' => __( 'Rajab', GPERSIANDATE_TEXTDOMAIN ), // (To Respect) رجب
					'08' => __( 'Shaaban', GPERSIANDATE_TEXTDOMAIN ), // Sha'bān (To Spread and Distribute) شعبان
					'09' => __( 'Ramadan', GPERSIANDATE_TEXTDOMAIN ), // Ramadān (Parched Thirst) رمضان
					'10' => __( 'Shawwal', GPERSIANDATE_TEXTDOMAIN ), // Shawwāl (To Be Light and Vigorous) شوّال
					'11' => __( 'Dhu al-Qidah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Qi'dah (The Master of Truce) ذو القعدة
					'12' => __( 'Dhu al-Hijjah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Hijjah (The Possessor of Hajj) ذو الحجة
				);

			break;
			default :
			case 'Jalali' :

				$months = array(
					'01' => __( 'Farvardin', GPERSIANDATE_TEXTDOMAIN ), // (31 days, فروردین)
					'02' => __( 'Ordibehesht', GPERSIANDATE_TEXTDOMAIN ), // (31 days, اردیبهشت)
					'03' => __( 'Khordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, خرداد)
					'04' => __( 'Tir', GPERSIANDATE_TEXTDOMAIN ), // (31 days, تیر)
					'05' => __( 'Mordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, مرداد)
					'06' => __( 'Shahrivar', GPERSIANDATE_TEXTDOMAIN ), // (31 days, شهریور)
					'07' => __( 'Mehr', GPERSIANDATE_TEXTDOMAIN ), // (30 days, مهر)
					'08' => __( 'Aban', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آبان)
					'09' => __( 'Azar', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آذر)
					'10' => __( 'Dey', GPERSIANDATE_TEXTDOMAIN ), // (30 days, دی)
					'11' => __( 'Bahman', GPERSIANDATE_TEXTDOMAIN ), // (30 days, بهمن)
					'12' => __( 'Esfand', GPERSIANDATE_TEXTDOMAIN ), // (29 days- 30 days in leap year, اسفند)
				);
		}

		if ( $all )
			return $months;

		return $months[zeroise($month, 2)];
	}

	public static function dayoftheweek( $dayoftheweek, $all = FALSE, $calendar = NULL )
	{
		if ( is_null( $calendar ) )
			$calendar = 'Jalali';

		switch( $calendar ) {

			case 'Gregorian': // 0 (for Sunday) through 6 (for Saturday)

				$week = array(
					0 => _x( 'Sunday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					1 => _x( 'Monday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					2 => _x( 'Tuesday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					3 => _x( 'Wednesday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					4 => _x( 'Thursday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					5 => _x( 'Friday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
					6 => _x( 'Saturday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
				);

			break;
			case 'Hijri': // 0 (for Saturday) through 6 (for Friday)

				// @SEE: https://en.wikipedia.org/wiki/Islamic_calendar
				$week = array(
					0 => _x( 'as-Sabt', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					1 => _x( 'al-Aḥad', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					2 => _x( 'al-Ithnayn', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					3 => _x( 'ath-Thulāthāʼ', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					4 => _x( 'al-Arbi‘ā’', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					5 => _x( 'al-Khamīs', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
					6 => _x( 'al-Jumu‘ah', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
				);

			default:
			case 'Jalali': // 0 (for Saturday) through 6 (for Friday)

				$week = array(
					0 => _x( 'Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					1 => _x( 'Yek-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					2 => _x( 'Do-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					3 => _x( 'Seh-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					4 => _x( 'Chahar-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					5 => _x( 'Panj-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
					6 => _x( 'Adineh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
				);
		}

		if ( $all )
			return $week;

		// if ( $dayoftheweek < 0 )
		// 	$dayoftheweek = 7;

		return $week[$dayoftheweek-1];
	}

	// FIXME: DEPRECATED
	// OLD: used in p2
	// before: get_dayoftheweek()
	public static function get_dayoftheweek( $dayoftheweek, $all = FALSE, $calendar = NULL )
	{
		return self::dayoftheweek( $dayoftheweek, $all, ( is_null( $calendar ) ? 'Gregorian' : $calendar ) );
	}
}
