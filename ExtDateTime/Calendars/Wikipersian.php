<?php

//TODO: add documentation comments
 
include_once( dirname(__FILE__)."/Persian.php" );
 
class ExtDateTime_Wikipersian extends ExtDateTime_Persian
{
	static private $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	static private $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29); 

	static private $j_months = array('Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad', 'Shahrivar', 'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand');

	public static function link( $link, $title, $atts = array( 'rel' => 'wiki' ) )
	{
		$html = '<a href="'.$link.'" ';
		foreach ( $atts as $key => $value )
			$html .= ' '.$key.'="'.$value.'"';
		return $html.'>'.$title.'</a>';
	}

	public function format( $format, $timezone = null, $local = GPERSIANDATE_LOCALE, $translate_numbers = true )
	{
		if ( ! class_exists( 'gPersianDateWiki' ) || ! class_exists( 'gPersianDate' ) )
			return parent::format( $format, $timezone );
	
 		if ( isset( $timezone ) ) {
			$tempTimezone = $this->getTimezone();
			$this->setTimezone( $timezone );
		}

		$result = "";

		//list( $year, $month, $day ) = explode('-', parent::format("Y-n-j"));
		list( $year, $month, $day ) = explode('-', ExtDateTime::format("Y-n-j"));
		list( $jyear, $jmonth, $jday ) = self::gregorianToJalali($year, $month, $day);

		for ($i = 0; $i < strlen($format); $i++) {
			switch ($format[$i]) {
				case "y": //A two digit representation of a year (Examples: 99 or 03)
					//$result .= substr($jyear, 2, 4);
					$result .= self::link( gPersianDateWiki::year( $jyear ), ( $translate_numbers ? gPersianDate::translate_numbers( substr( $jyear, 2, 4 ), $local ) : substr( $jyear, 2, 4 ) ) );
					break;

				case "Y": //A full numeric representation of a year, 4 digits (Examples: 1999 or 2003)
					//$result .= $jyear;
					$result .= self::link( gPersianDateWiki::year( $jyear ), ( $translate_numbers ? gPersianDate::translate_numbers( $jyear, $local ) : $jyear ) );
					break;

				case "M": //There is no short textual representation of months in persian so we use full textual representaions instead.
				case "F": //A full textual representation of a month (Farvardin through Esfand)
					//$result .= $this->translate(self::$j_months[$jmonth-1]);
					$result .= self::link( gPersianDateWiki::month( $jmonth ), $this->translate( self::$j_months[$jmonth-1] ) );
					break;

				case "m": //Numeric representation of a month, with leading zeros (01 through 12)
					//$result .= sprintf('%02d', $jmonth);
					$result .= self::link( gPersianDateWiki::month( $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( sprintf( '%02d', $jmonth ), $local ) : sprintf( '%02d', $jmonth ) ) );
					break;

				case "n": //Numeric representation of a month, without leading zeros (1 through 12)
					//$result .= $jmonth;
					$result .= self::link( gPersianDateWiki::month( $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( $jmonth, $local ) : $jmonth ) );
					break;

				case "d": //Day of the month, 2 digits with leading zeros (01 to 31)
					//$result .= sprintf('%02d', $jday);
					$result .= self::link( gPersianDateWiki::day( $jday, $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( sprintf( '%02d', $jday ), $local ) : sprintf( '%02d', $jday ) ) );
					break;

				case "j": //Day of the month without leading zeros (1 to 31)
					//$result .= $jday;
					$result .= self::link( gPersianDateWiki::day( $jday, $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( $jday, $local ) : $jday ) );
					break;

				case "w": //Numeric representation of the day of the week (0 (for Saturday) through 6 (for Friday))
					$temp = (ExtDateTime::format("w") + 1) % 7;
					$result .= self::link( gPersianDateWiki::dayoftheweek( $temp ), ( $translate_numbers ? gPersianDate::translate_numbers( $temp, $local ) : $temp ) );
					break;

				case "t": //Number of days in the given month (29 through 31)
					if ($jmonth < 12) $temp = self::$j_days_in_month[$jmonth-1];
					else if (self::jalaliCheckDate($jmonth, 30, $jyear)) $temp = '30';
					else $temp = '29';
					$result .= self::link( gPersianDateWiki::month( $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( $temp, $local ) : $temp ) );
					break;

				case "z": //The day of the year starting from 0 (0 through 365)
					$day_of_year = 0;
					for ($n=0; $n<$jmonth-1; $n++) {
						$day_of_year += self::$j_days_in_month[$n];
					}
					$day_of_year += $jday-1;
					$result .= self::link( gPersianDateWiki::day( $jday, $jmonth ), ( $translate_numbers ? gPersianDate::translate_numbers( $day_of_year, $local ) : $day_of_year ) );
					break;

				case "L": //Whether it's a leap year (1 if it is a leap year, 0 otherwise.)
					$temp = self::jalaliCheckDate(12, 30, $jyear) ? '1' : '0' ;
					$result .= self::link( gPersianDateWiki::year( $jyear ), ( $translate_numbers ? gPersianDate::translate_numbers( $temp, $local ) : $temp ) );
					break;

				case "W": //Week number of year, weeks starting on Saturday
					$z = $this->format('z');
					$firstSaturday = ($z - $this->format('w') + 7) % 7;
					$days = $z - $firstSaturday; //Number of days after the first Saturday of the year
					if ($days < 0) {
						$z += self::jalaliCheckDate(12, 30, $jyear-1) ? 366 : 365;
						$firstSaturday = ($z - $this->format('w') + 7) % 7;
						$days = $z - $firstSaturday; 
					}
					$temp = floor($days / 7) + 1;
					$result .= self::link( gPersianDateWiki::year( $jyear ), ( $translate_numbers ? gPersianDate::translate_numbers( $temp, $local ) : $temp ) );
					break;
					
				case "\\":
					if ($i+1 < strlen($format)) $result .= $format[++$i];
					else $result .= $format[$i];
					break;

				case "l":
					$temp = ExtDateTime::format( "l" );
					$result .= self::link( gPersianDateWiki::dayoftheweek( ( (ExtDateTime::format("w") + 1) % 7 ) ), $this->translate( $temp ) );
					break;
					
				default:
					$result .= ExtDateTime::format($format[$i]);
			}
		}

		if (isset($timezone)) {
			$this->setTimezone($tempTimezone);
		}

		return $result;
	}
	
	public static function cal( $for, $f )
	{
		$y = array();
		$j = $w = $d = 1;
		
		foreach ( self::$j_days_in_month as $m => $s ) {
			for ( $i = 0; $i < $s; $i++ ) {
				$y[$m+1][$w][$i+1] = $j;
				$j++;
				$f++;
				if ( $f == 8 ) { 
					$f = 1;
					$w++;
				}
				
			}
		}
	
		return $y;
	
		return array(
			'month' => array( // mah avval
				'week of year' => array( // hafte avval
					'day of month' => 'day of year' // rooz aval
				),
			),
		);
		
	}
}
?>