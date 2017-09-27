<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateStrings extends gPersianDateModuleCore
{

	// @REF: [Month Dropdown in PHP](http://paulferrett.com/2012/month-dropdown-in-php/)
	public static function lastMonths( $limit = 12, $calendar = NULL )
	{
		static $strings = [];

		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( ! isset( $strings[$calendar][$limit] ) ) {

			$months = [];

			for ( $i = 0; $i <= $limit; ++$i ) {

				$time = strtotime( sprintf( '-%d months', $i ) );

				if ( 'Gregorian' == $calendar ) {

					$key = date( 'Y-m', $time );
					$val = date( 'F Y', $time );

				} else {

					$key = gPersianDateDateTime::to( $time, 'Y-m', GPERSIANDATE_TIMEZONE, $calendar );
					$val = gPersianDateDateTime::to( $time, 'F Y', GPERSIANDATE_TIMEZONE, $calendar );
				}

				$months[$key] = $val;
			}

			$strings[$calendar][$limit] = $months;
		}

		return $strings[$calendar][$limit];
	}

	// @SEE: http://www.wikiwand.com/en/Month
	public static function month( $formatted = '01', $all = FALSE, $calendar = NULL, $abbrev = FALSE )
	{
		static $strings = [];

		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );
		$full     = $abbrev ? 'abbrev' : 'full';

		if ( ! isset( $strings[$calendar][$full] ) ) {

			switch ( $calendar ) {

				case 'Gregorian':

					if ( $abbrev )
						$strings[$calendar]['abbrev'] = [
							'01' => _x( 'Jan', 'Strings: Month: Gregorian: Abbreviation: January', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'02' => _x( 'Feb', 'Strings: Month: Gregorian: Abbreviation: February', GPERSIANDATE_TEXTDOMAIN ), // 28 days, 29 in leap years
							'03' => _x( 'Mar', 'Strings: Month: Gregorian: Abbreviation: March', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'04' => _x( 'Apr', 'Strings: Month: Gregorian: Abbreviation: April', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'05' => _x( 'May', 'Strings: Month: Gregorian: Abbreviation: May', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'06' => _x( 'Jun', 'Strings: Month: Gregorian: Abbreviation: June', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'07' => _x( 'Jul', 'Strings: Month: Gregorian: Abbreviation: July', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'08' => _x( 'Aug', 'Strings: Month: Gregorian: Abbreviation: August', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'09' => _x( 'Sep', 'Strings: Month: Gregorian: Abbreviation: September', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'10' => _x( 'Oct', 'Strings: Month: Gregorian: Abbreviation: October', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'11' => _x( 'Nov', 'Strings: Month: Gregorian: Abbreviation: November', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'12' => _x( 'Dec', 'Strings: Month: Gregorian: Abbreviation: December', GPERSIANDATE_TEXTDOMAIN ), // 31 days
						];

					else
						$strings[$calendar]['full'] = [
							'01' => _x( 'January', 'Strings: Month: Gregorian: Full: January', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'02' => _x( 'February', 'Strings: Month: Gregorian: Full: February', GPERSIANDATE_TEXTDOMAIN ), // 28 days, 29 in leap years
							'03' => _x( 'March', 'Strings: Month: Gregorian: Full: March', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'04' => _x( 'April', 'Strings: Month: Gregorian: Full: April', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'05' => _x( 'May', 'Strings: Month: Gregorian: Full: May', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'06' => _x( 'June', 'Strings: Month: Gregorian: Full: June', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'07' => _x( 'July', 'Strings: Month: Gregorian: Full: July', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'08' => _x( 'August', 'Strings: Month: Gregorian: Full: August', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'09' => _x( 'September', 'Strings: Month: Gregorian: Full: September', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'10' => _x( 'October', 'Strings: Month: Gregorian: Full: October', GPERSIANDATE_TEXTDOMAIN ), // 31 days
							'11' => _x( 'November', 'Strings: Month: Gregorian: Full: November', GPERSIANDATE_TEXTDOMAIN ), // 30 days
							'12' => _x( 'December', 'Strings: Month: Gregorian: Full: December', GPERSIANDATE_TEXTDOMAIN ), // 31 days
						];

				break;
				case 'Hijri':

					// 'محرم', 'صفر ', 'ربیع‌الاول', 'ربیع‌الثانی', 'جمادی‌الاول', 'جمادی‌الثانی', 'رجب', 'شعبان', 'رمضان', 'شوال', 'ذیقعده', 'ذیحجه'
					// 'محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني', 'جمادى الأولى', 'جمادى الآخرة', 'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة'

					if ( $abbrev )
						$strings[$calendar]['abbrev'] = [
							'01' => _x( 'Muh', 'Strings: Month: Hijri: Abbreviation: Muharram', GPERSIANDATE_TEXTDOMAIN ), // (Restricted/sacred) محرّم
							'02' => _x( 'Saf', 'Strings: Month: Hijri: Abbreviation: Safar', GPERSIANDATE_TEXTDOMAIN ), // (Empty/Yellow) صفر
							'03' => _x( 'Rb1', 'Strings: Month: Hijri: Abbreviation: Rabi Al Awwal', GPERSIANDATE_TEXTDOMAIN ), // Rabī' al-Awwal/Rabi' I (First Spring) ربيع الأول
							'04' => _x( 'Rb2', 'Strings: Month: Hijri: Abbreviation: Rabi Al Thani', GPERSIANDATE_TEXTDOMAIN ), // Rabī’ ath-Thānī/Rabi` al-Aakhir/Rabi' II (Second spring or Last spring) ربيع الآخر أو ربيع الثاني
							'05' => _x( 'Jm1', 'Strings: Month: Hijri: Abbreviation: Jumada Al Oula', GPERSIANDATE_TEXTDOMAIN ), // Jumada al-Awwal/Jumaada I (First Freeze) جمادى الأول
							'06' => _x( 'Jm2', 'Strings: Month: Hijri: Abbreviation: Jumada Al Akhira', GPERSIANDATE_TEXTDOMAIN ), // Jumada ath-Thānī or Jumādā al-Thānī/Jumādā II (Second Freeze or Last Freeze) جمادى الآخر أو جمادى الثاني
							'07' => _x( 'Raj', 'Strings: Month: Hijri: Abbreviation: Rajab', GPERSIANDATE_TEXTDOMAIN ), // (To Respect) رجب
							'08' => _x( 'Sha', 'Strings: Month: Hijri: Abbreviation: Shaban', GPERSIANDATE_TEXTDOMAIN ), // Sha'bān (To Spread and Distribute) شعبان
							'09' => _x( 'Ram', 'Strings: Month: Hijri: Abbreviation: Ramadan', GPERSIANDATE_TEXTDOMAIN ), // Ramadān (Parched Thirst) رمضان
							'10' => _x( 'Shw', 'Strings: Month: Hijri: Abbreviation: Shawwal', GPERSIANDATE_TEXTDOMAIN ), // Shawwāl (To Be Light and Vigorous) شوّال
							'11' => _x( 'Qid', 'Strings: Month: Hijri: Abbreviation: Dhul Qidah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Qi'dah (The Master of Truce) ذو القعدة
							'12' => _x( 'Hij', 'Strings: Month: Hijri: Abbreviation: Dhul Hijjah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Hijjah (The Possessor of Hajj) ذو الحجة
						];

					else
						$strings[$calendar]['full'] = [
							'01' => _x( 'Muharram', 'Strings: Month: Hijri: Full: Muharram', GPERSIANDATE_TEXTDOMAIN ), // (Restricted/sacred) محرّم
							'02' => _x( 'Safar', 'Strings: Month: Hijri: Full: Safar', GPERSIANDATE_TEXTDOMAIN ), // (Empty/Yellow) صفر
							'03' => _x( 'Rabi Al Awwal', 'Strings: Month: Hijri: Full: Rabi Al Awwal', GPERSIANDATE_TEXTDOMAIN ), // Rabī' al-Awwal/Rabi' I (First Spring) ربيع الأول
							'04' => _x( 'Rabi Al Thani', 'Strings: Month: Hijri: Full: Rabi Al Thani', GPERSIANDATE_TEXTDOMAIN ), // Rabī’ ath-Thānī/Rabi` al-Aakhir/Rabi' II (Second spring or Last spring) ربيع الآخر أو ربيع الثاني
							'05' => _x( 'Jumada Al Oula', 'Strings: Month: Hijri: Full: Jumada Al Oula', GPERSIANDATE_TEXTDOMAIN ), // Jumada al-Awwal/Jumaada I (First Freeze) جمادى الأول
							'06' => _x( 'Jumada Al Akhira', 'Strings: Month: Hijri: Full: Jumada Al Akhira', GPERSIANDATE_TEXTDOMAIN ), // Jumada ath-Thānī or Jumādā al-Thānī/Jumādā II (Second Freeze or Last Freeze) جمادى الآخر أو جمادى الثاني
							'07' => _x( 'Rajab', 'Strings: Month: Hijri: Full: Rajab', GPERSIANDATE_TEXTDOMAIN ), // (To Respect) رجب
							'08' => _x( 'Shaban', 'Strings: Month: Hijri: Full: Shaban', GPERSIANDATE_TEXTDOMAIN ), // Sha'bān (To Spread and Distribute) شعبان
							'09' => _x( 'Ramadan', 'Strings: Month: Hijri: Full: Ramadan', GPERSIANDATE_TEXTDOMAIN ), // Ramadān (Parched Thirst) رمضان
							'10' => _x( 'Shawwal', 'Strings: Month: Hijri: Full: Shawwal', GPERSIANDATE_TEXTDOMAIN ), // Shawwāl (To Be Light and Vigorous) شوّال
							'11' => _x( 'Dhul Qidah', 'Strings: Month: Hijri: Full: Dhul Qidah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Qi'dah (The Master of Truce) ذو القعدة
							'12' => _x( 'Dhul Hijjah', 'Strings: Month: Hijri: Full: Dhul Hijjah', GPERSIANDATE_TEXTDOMAIN ), // Dhu al-Hijjah (The Possessor of Hajj) ذو الحجة
						];

				break;
				default:
				case 'Jalali':

					if ( $abbrev )
						$strings[$calendar]['abbrev'] = [
							'01' => _x( 'Far', 'Strings: Month: Jalali: Abbreviation: Farvardin', GPERSIANDATE_TEXTDOMAIN ), // (31 days, فروردین)
							'02' => _x( 'Ord', 'Strings: Month: Jalali: Abbreviation: Ordibehesht', GPERSIANDATE_TEXTDOMAIN ), // (31 days, اردیبهشت)
							'03' => _x( 'Kho', 'Strings: Month: Jalali: Abbreviation: Khordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, خرداد)
							'04' => _x( 'Tir', 'Strings: Month: Jalali: Abbreviation: Tir', GPERSIANDATE_TEXTDOMAIN ), // (31 days, تیر)
							'05' => _x( 'Mor', 'Strings: Month: Jalali: Abbreviation: Mordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, مرداد)
							'06' => _x( 'Sha', 'Strings: Month: Jalali: Abbreviation: Shahrivar', GPERSIANDATE_TEXTDOMAIN ), // (31 days, شهریور)
							'07' => _x( 'Meh', 'Strings: Month: Jalali: Abbreviation: Mehr', GPERSIANDATE_TEXTDOMAIN ), // (30 days, مهر)
							'08' => _x( 'Aba', 'Strings: Month: Jalali: Abbreviation: Aban', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آبان)
							'09' => _x( 'Aza', 'Strings: Month: Jalali: Abbreviation: Azar', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آذر)
							'10' => _x( 'Dey', 'Strings: Month: Jalali: Abbreviation: Dey', GPERSIANDATE_TEXTDOMAIN ), // (30 days, دی)
							'11' => _x( 'Bah', 'Strings: Month: Jalali: Abbreviation: Bahman', GPERSIANDATE_TEXTDOMAIN ), // (30 days, بهمن)
							'12' => _x( 'Esf', 'Strings: Month: Jalali: Abbreviation: Esfand', GPERSIANDATE_TEXTDOMAIN ), // (29 days- 30 days in leap year, اسفند)
						];

					else
						$strings[$calendar]['full'] = [
							'01' => _x( 'Farvardin', 'Strings: Month: Jalali: Full: Farvardin', GPERSIANDATE_TEXTDOMAIN ), // (31 days, فروردین)
							'02' => _x( 'Ordibehesht', 'Strings: Month: Jalali: Full: Ordibehesht', GPERSIANDATE_TEXTDOMAIN ), // (31 days, اردیبهشت)
							'03' => _x( 'Khordad', 'Strings: Month: Jalali: Full: Khordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, خرداد)
							'04' => _x( 'Tir', 'Strings: Month: Jalali: Full: Tir', GPERSIANDATE_TEXTDOMAIN ), // (31 days, تیر)
							'05' => _x( 'Mordad', 'Strings: Month: Jalali: Full: Mordad', GPERSIANDATE_TEXTDOMAIN ), // (31 days, مرداد)
							'06' => _x( 'Shahrivar', 'Strings: Month: Jalali: Full: Shahrivar', GPERSIANDATE_TEXTDOMAIN ), // (31 days, شهریور)
							'07' => _x( 'Mehr', 'Strings: Month: Jalali: Full: Mehr', GPERSIANDATE_TEXTDOMAIN ), // (30 days, مهر)
							'08' => _x( 'Aban', 'Strings: Month: Jalali: Full: Aban', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آبان)
							'09' => _x( 'Azar', 'Strings: Month: Jalali: Full: Azar', GPERSIANDATE_TEXTDOMAIN ), // (30 days, آذر)
							'10' => _x( 'Dey', 'Strings: Month: Jalali: Full: Dey', GPERSIANDATE_TEXTDOMAIN ), // (30 days, دی)
							'11' => _x( 'Bahman', 'Strings: Month: Jalali: Full: Bahman', GPERSIANDATE_TEXTDOMAIN ), // (30 days, بهمن)
							'12' => _x( 'Esfand', 'Strings: Month: Jalali: Full: Esfand', GPERSIANDATE_TEXTDOMAIN ), // (29 days- 30 days in leap year, اسفند)
						];
			}
		}

		if ( $all )
			return $strings[$calendar][$full];

		$key = zeroise( $formatted, 2 );

		if ( isset( $strings[$calendar][$full][$key] ) )
			return $strings[$calendar][$full][$key];

		return $formatted;
	}

	public static function meridiemAntePost( $formatted, $all = FALSE, $calendar = NULL )
	{
		static $strings = NULL;

		if ( is_null( $strings ) )
			$strings = [
				'Gregorian' => [
					'am' => _x( 'am', 'Ante meridiem: Gregorian - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'pm' => _x( 'pm', 'Post meridiem: Gregorian - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'AM' => _x( 'AM', 'Ante meridiem: Gregorian - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
					'PM' => _x( 'PM', 'Post meridiem: Gregorian - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
				],
				'Hijri' => [
					'am' => _x( 'am', 'Ante meridiem: Hijri - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'pm' => _x( 'pm', 'Post meridiem: Hijri - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'AM' => _x( 'AM', 'Ante meridiem: Hijri - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
					'PM' => _x( 'PM', 'Post meridiem: Hijri - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
				],
				'Jalali' => [
					'am' => _x( 'am', 'Ante meridiem: Jalali - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'pm' => _x( 'pm', 'Post meridiem: Jalali - Lowercase', GPERSIANDATE_TEXTDOMAIN ),
					'AM' => _x( 'AM', 'Ante meridiem: Jalali - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
					'PM' => _x( 'PM', 'Post meridiem: Jalali - Uppercase', GPERSIANDATE_TEXTDOMAIN ),
				],
			];

		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );

		if ( $all )
			return $strings[$calendar];

		if ( isset( $strings[$calendar][$formatted] ) )
			return $strings[$calendar][$formatted];

		return $formatted;
	}

	public static function dayoftheweek( $formatted, $all = FALSE, $calendar = NULL, $initial = FALSE )
	{
		static $strings = [];

		$calendar = gPersianDateDateTime::sanitizeCalendar( $calendar );
		$full     = $initial ? 'initial' : 'full';

		if ( ! isset( $strings[$calendar][$full] ) ) {

			switch ( $calendar ) {

				case 'Gregorian':

					// 0 (for Sunday) through 6 (for Saturday)

					if ( $initial )
						$strings['Gregorian']['initial'] = [
							_x( 'S', 'Day of the Week Initial: Gregorian - Sunday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'M', 'Day of the Week Initial: Gregorian - Monday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'T', 'Day of the Week Initial: Gregorian - Tuesday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'W', 'Day of the Week Initial: Gregorian - Wednesday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'T', 'Day of the Week Initial: Gregorian - Thursday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'F', 'Day of the Week Initial: Gregorian - Friday', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'S', 'Day of the Week Initial: Gregorian - Saturday', GPERSIANDATE_TEXTDOMAIN ),
						];

					else
						$strings['Gregorian']['full'] = [
							_x( 'Sunday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Monday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Tuesday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Wednesday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Thursday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Friday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Saturday', 'Day of the Week: Gregorian', GPERSIANDATE_TEXTDOMAIN ),
						];

				break;
				case 'Hijri':

					// @SEE: https://en.wikipedia.org/wiki/Islamic_calendar
					// 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'

					if ( $initial )
						$strings['Hijri']['initial'] = [
							_x( 'Ah', 'Day of the Week Initial: Hijri - al-Ahad', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'It', 'Day of the Week Initial: Hijri - al-Ithnayn', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Th', 'Day of the Week Initial: Hijri - ath-Thulatha', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Ar', 'Day of the Week Initial: Hijri - al-Arbia', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Kh', 'Day of the Week Initial: Hijri - al-Khamis', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Ju', 'Day of the Week Initial: Hijri - l-Jumuah', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Sa', 'Day of the Week Initial: Hijri - as-Sabt', GPERSIANDATE_TEXTDOMAIN ),
						];

					else
						$strings['Hijri']['full'] = [
							_x( 'al-Aḥad', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'al-Ithnayn', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'ath-Thulāthāʼ', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'al-Arbi‘ā’', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'al-Khamīs', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'al-Jumu‘ah', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'as-Sabt', 'Day of the Week: Hijri', GPERSIANDATE_TEXTDOMAIN ),
						];

				default:
				case 'Jalali':

					if ( $initial )
						$strings['Jalali']['initial'] = [
							_x( 'Ye', 'Day of the Week Initial: Jalali - Yek-Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Do', 'Day of the Week Initial: Jalali - Do-Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Se', 'Day of the Week Initial: Jalali - Seh-Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Ch', 'Day of the Week Initial: Jalali - Chahar-Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Pa', 'Day of the Week Initial: Jalali - Panj-Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Ad', 'Day of the Week Initial: Jalali - Adineh', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Sh', 'Day of the Week Initial: Jalali - Shanbeh', GPERSIANDATE_TEXTDOMAIN ),
						];

					else
						$strings['Jalali']['full'] = [
							_x( 'Yek-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Do-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Seh-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Chahar-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Panj-Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Adineh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
							_x( 'Shanbeh', 'Day of the Week: Jalali', GPERSIANDATE_TEXTDOMAIN ),
						];
			}
		}

		if ( $all )
			return $strings[$calendar][$full];

		if ( isset( $strings[$calendar][$full][$formatted] ) )
			return $strings[$calendar][$full][$formatted];

		return $formatted;
	}

	// FIXME: DEPRECATED
	// OLD: used in p2
	// before: get_dayoftheweek()
	public static function get_dayoftheweek( $dayoftheweek, $all = FALSE, $calendar = NULL )
	{
		return self::dayoftheweek( $dayoftheweek, $all, ( is_null( $calendar ) ? 'Gregorian' : $calendar ) );
	}
}
