<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateStrings extends gPersianDateModuleCore
{
	// before: get_month()
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

			case 'Gregorian' :

				$week = array(
					0 => __( 'Saturday', GPERSIANDATE_TEXTDOMAIN ),
					1 => __( 'Sunday', GPERSIANDATE_TEXTDOMAIN ),
					2 => __( 'Monday', GPERSIANDATE_TEXTDOMAIN ),
					3 => __( 'Tuesday', GPERSIANDATE_TEXTDOMAIN ),
					4 => __( 'Wednesday', GPERSIANDATE_TEXTDOMAIN ),
					5 => __( 'Thursday', GPERSIANDATE_TEXTDOMAIN ),
					6 => __( 'Friday', GPERSIANDATE_TEXTDOMAIN ),
				);

			break;
			case 'Hijri' :

				// FIXME: !!

			default :
			case 'Jalali' :

				$week = array(
					0 => __( 'Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
					1 => __( 'YekShanbeh', GPERSIANDATE_TEXTDOMAIN ),
					2 => __( 'DoShanbeh', GPERSIANDATE_TEXTDOMAIN ),
					3 => __( 'SeShanbeh', GPERSIANDATE_TEXTDOMAIN ),
					4 => __( 'ChaharShanbeh', GPERSIANDATE_TEXTDOMAIN ),
					5 => __( 'PanjShanbeh', GPERSIANDATE_TEXTDOMAIN ),
					6 => __( 'Jom\'eh', GPERSIANDATE_TEXTDOMAIN ),
				);
		}

		if ( $all )
			return $week;

		// if ( $dayoftheweek < 0 )
		// 	$dayoftheweek = 7;

		return $week[$dayoftheweek-1];
	}

	// OLD: used in p2
	// before: get_dayoftheweek()
	// (0 (for Saturday) through 6 (for Friday))
	public static function get_dayoftheweek( $dayoftheweek, $all = FALSE, $calendar = NULL )
	{
		$week = array(
			0 => __( 'Sunday', GPERSIANDATE_TEXTDOMAIN ),
			1 => __( 'Monday', GPERSIANDATE_TEXTDOMAIN ),
			2 => __( 'Tuesday', GPERSIANDATE_TEXTDOMAIN ),
			3 => __( 'Wednesday', GPERSIANDATE_TEXTDOMAIN ),
			4 => __( 'Thursday', GPERSIANDATE_TEXTDOMAIN ),
			5 => __( 'Friday', GPERSIANDATE_TEXTDOMAIN ),
			6 => __( 'Saturday', GPERSIANDATE_TEXTDOMAIN ),
		);

		if ( $all )
			return $week;

		return $week[$dayoftheweek-1];
	}
}
