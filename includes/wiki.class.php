<?php defined( 'ABSPATH' ) or die( 'Restricted access' );
/**

	--
	Use like this to cache wiki pages:
	Add Cacheable Gists to Your WordPress Site
	https://gist.github.com/tollmanz/2864688
	http://tollmanz.com/pretty-cacheable-gists/
	--









**/

class gPersianDateWiki
{
	//var $_wiki_info = 'http://fa.wikipedia.org/wiki/%DA%AF%D8%A7%D9%87%E2%80%8C%D8%B4%D9%85%D8%A7%D8%B1%DB%8C'; // ÇåÔãÇÑí
	//var $_wiki_cal = 'http://fa.wikipedia.org/wiki/%D8%AA%D9%82%D9%88%DB%8C%D9%85_%D8%B1%D8%B3%D9%85%DB%8C_%D8%A7%DB%8C%D8%B1%D8%A7%D9%86'; // ÇåÔãÇÑí ÏÑ ÇíÑÇä
	
	
	public static function date( $format, $time = null, $time_zone = GPERSIANDATE_TIMEZONE, $local = GPERSIANDATE_LOCALE, $translate_numbers = true ) 
	{
		if ( gPersianDate::check_iso( $format ) ) 
			return mysql2date( $format, ( is_null( $time ) ? current_time( 'timestamp' ) : $time ), false ); 
		
		$date = ExtDateTime::factory( 'Wikipersian', $time, 'UTC', array( 'gPersianDate', 'translator' ) );
		return $date->format( $format, null, $local, $translate_numbers );
	}
	
	// see : get_calendar()
	public static function cal( $for ) 
	{
		$date = ExtDateTime::factory( 'Wikipersian', null, 'UTC', array( 'gPersianDate', 'translator' ) );		
		
		list( $gyear, $gmonth, $gday ) = $date->jalaliToGregorian( $for, 1, 1 );
		$first = ExtDateTime::factory( 'Persian', strtotime( $gday.'-'.$gmonth.'-'.$gyear ) );
  		$f = $first->format( 'w' ); 	

		$f++;

		
		$cal = $date->cal( $for, $f );
	
		
		$m = $w = $d = $s = 1;
		foreach( $cal as $month => $weekofmonth ) {
			echo '<table style="border:1px solid gray; float:left; margin:20px 20px 20px 0"><tbody>';
			foreach ( $weekofmonth as $dayofmonth => $dayofyear ) {
				echo '<tr>';
				if ( $m == 1 )
					$s = $f;
				foreach ( $dayofyear as $key => $val ) {
					echo '<td colspan="'.$s.'" title="'.$key.'::'.$val.'">'.$d.'</td>';
					$d++;
					$s = 1;
				}
				echo '</tr>';
				
			}
			$d = 1;
			echo '</tbody></table>';
		
		}
		
	
	}
	
	
	public static function get_url( $local = GPERSIANDATE_LOCALE )
	{
		switch ( $local ) {
			case 'fa_IR' : return 'http://fa.wikipedia.org/wiki/';
		}
		return 'http://en.wikipedia.org/wiki/';
	}
	
	// http://fa.wikipedia.org/wiki/%DB%B2%DB%B9_%D8%AE%D8%B1%D8%AF%D8%A7%D8%AF
	// http://fa.wikipedia.org/wiki/%DB%B2%DB%B4_%D9%85%D9%87
	public static function day( $day, $month, $cal = 'persian', $local = GPERSIANDATE_LOCALE )
	{
		switch ( $cal ) {
			default :
			case 'persian' : return self::get_url( $local ).gPersianDate::translate_numbers( $day, $local ).'_'.gPersianDate::get_month( $month );
		}
		
		// use $wp_local global for mounth name 
		//return self::get_url( $local ).gPersianDate::translate_numbers( $day, $local ).'_'.gPersianDate::get_month( $month );

		return self::get_url( $local );
	}
	
	// http://fa.wikipedia.org/wiki/%D8%A7%D8%B1%D8%AF%DB%8C%D8%A8%D9%87%D8%B4%D8%AA
	public static function month( $month, $cal = 'persian', $local = GPERSIANDATE_LOCALE )
	{
		switch ( $cal ) {
			default :
			case 'persian' : return self::get_url( $local ).gPersianDate::get_month( $month );
		}
	}
	
	// http://fa.wikipedia.org/wiki/%DB%B1%DB%B3%DB%B9%DB%B2
	public static function year( $year, $cal = 'persian', $local = GPERSIANDATE_LOCALE )
	{
		switch ( $cal ) {
			default :
			case 'persian' : return self::get_url( $local ).gPersianDate::translate_numbers( $year );
		}
	}
	
	// http://fa.wikipedia.org/wiki/%D8%B3%D9%87%E2%80%8C%D8%B4%D9%86%D8%A8%D9%87
	public static function dayoftheweek( $dayoftheweek, $cal = 'persian', $local = GPERSIANDATE_LOCALE )
	{
		return self::get_url( $local ).gPersianDate::get_dayoftheweek( $dayoftheweek );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public static function getToday( $cal = 'persian' )
	{
	
	}
	
	// http://php.net/manual/en/function.urlencode.php#92424
	public static function url_encode( $string )
	{
        return urlencode( utf8_encode( $string ) );
    }
    
	// http://php.net/manual/en/function.urlencode.php#92424
    public static function url_decode( $string )
	{
        return utf8_decode( urldecode( $string ) );
    }	
	
	/**
	
// http://www.freakcommander.de/4967/computer/wordpress/wp-plugin-permalink-encoding/

function raw_title( $title, $raw_title="", $context="" ) {
	if ( $context == 'save' )
		return $raw_title;
	else
		return $title;
}

function custom_permalinks($title) {
	$title = sanitize_title_with_dashes($title);
	$toupper = create_function('$m', 'return strtoupper($m[0]);');
	$title = preg_replace_callback('/(%[0-9a-f]{2}?)+/', $toupper, $title);
	return $title;
}

remove_filter('sanitize_title', 'sanitize_title_with_dashes');
add_filter( 'sanitize_title', 'raw_title', 9, 3 );
add_filter( 'sanitize_title', 'custom_permalinks' , 10);	
	
	
	
	**/
	
}