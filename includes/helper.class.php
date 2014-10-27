<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

// must dep, not used anywhere
if ( ! class_exists( 'gPersianDateHelper' ) ) { class gPersianDateHelper
{
    // TODO : Must use the farhadi's
    
    
	static private $g_days_in_month = array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
	static private $j_days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 ); 

    function convert_back( $date, $time = '00:00', $time_zone = '', $format = false )
    {
        list( $year, $month, $day) = preg_split('%\/%', $date );
        list( $hour, $minute ) = preg_split( '%\:%', $time );
        list( $endate['year'], $endate['mon'], $endate['mday'] ) = self::jalali_to_gregorian( $year, $month, $day );
        // Y-m-d H:i:s UTC
        $the_string = $endate['year'].'-'.$endate['mon'].'-'.$endate['mday'].' '.$hour.':'.$minute.':00 '.$time_zone;
        //echo $the_string; die();
        $the_date = strtotime( $the_string );
        if ( false === $format )
            return $the_date;
        
        return date( $format, $the_date );
    }

    function j_last_day_of_month( $month_number = 1 )
	{
		return self::$j_days_in_month[$month_number-1];
	}
	
    function convert_back_to_OLD( $date, $time = '00:00', $gmt = true )
    {
        //$date = gimporter_con_back_num( $date );

        // To parse a date which may be delimited with slashes, dots, or hyphens : http://php.net/manual/en/function.split.php
        //list($year, $month, $day) = preg_split('[/.-]', $date);
        
        list( $year, $month, $day) = preg_split('%\/%', $date );
        list( $hour, $minute ) = preg_split( '%\:%', $time );
        //list( $hour, $minute ) = array( '00', '00' );
        $endate = array();
        list( $endate['year'], $endate['mon'], $endate['mday'] ) = self::jalali_to_gregorian( $year, $month, $day );
        //return date( $format, mktime( 0, 1, 0, $endate['mon'], $endate['mday'], $endate['year'] ) );
        $func = ( $gmt ? 'gmmktime' : 'mktime' );
        return $func( $hour, $minute, 0, $endate['mon'], $endate['mday'], $endate['year'] );
    }

    function convert_back_to_OLDer( $date, $format = 'Y-m-d H:i:s' )
    {
        //$date = gimporter_con_back_num( $date );

        //list($year, $month, $day) = preg_split('[/.-]', $date);
        list($year, $month, $day) = preg_split('%\/%', $date);
        //$endate = jd_to_gregorian(persian_to_jd($year, $month, $day));
        //return date("Y-m-d H:i:s", mktime ( 0, 0, 0, $endate['mon'], $endate['mday'], $endate['year'] ));
        $endate = array();
        //echo '<br /><br />month:'.$month.'<br />day:'.$day.'<br />year:'.$year.'<br /><br />';
        list($endate['year'], $endate['mon'], $endate['mday']) = self::jalali_to_gregorian($year, $month, $day);
        //echo '<br /><br />month:'.$endate['mon'].'<br />day:'.$endate['mday'].'<br />year:'.$endate['year'].'<br /><br />';
        return date( $format, mktime( 0, 1, 0, $endate['mon'], $endate['mday'], $endate['year'] ) );
    }

    ////////////////////////////////////////////////////////////////////////////
    // Farsiweb.info Jaladi/Gregorian Convertion Functions
    
    function div($a, $b)
    {
        return (int) ($a / $b);
    }

    function jalali_to_gregorian($j_y, $j_m, $j_d)
    {
        //global $g_days_in_month;
        //global $j_days_in_month;

        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);
        for ($i=0; $i < $jm; ++$i)
        $j_day_no += self::$j_days_in_month[$i];

        $j_day_no += $jd;

        $g_day_no = $j_day_no+79;

        $gy = 1600 + 400*self::div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
        {
            $g_day_no--;
            $gy += 100*self::div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365)
            $g_day_no++;
            else
            $leap = false;
        }

        $gy += 4*self::div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;

            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= self::$g_days_in_month[$i] + ($i == 1 && $leap); $i++)
        $g_day_no -= self::$g_days_in_month[$i] + ($i == 1 && $leap);
        $gm = $i+1;
        $gd = $g_day_no+1;

        return array($gy, $gm, $gd);
    }

    // NO NEED
    function jcheckdate($j_m, $j_d, $j_y)
    {
        if ($j_y < 0 || $j_y > 32767 || $j_m < 1 || $j_m > 12 || $j_d < 1 || $j_d >
        (self::$j_days_in_month[$j_m-1] + ($j_m == 12 && !(($j_y-979)%33%4))))
        return false;
        return true;
    }

    // NO NEED
    function gregorian_week_day($g_y, $g_m, $g_d)
    {
        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+self::div($gy+3,4)-self::div($gy+99,100)+self::div($gy+399,400);

        for ($i=0; $i < $gm; ++$i)
        $g_day_no += self::$g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
        /* leap and after Feb */
        ++$g_day_no;
        $g_day_no += $gd;

        return ($g_day_no + 5) % 7 + 1;
    }

    // NO NEED
    function jalali_week_day($j_y, $j_m, $j_d)
    {
        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;

        $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);

        for ($i=0; $i < $jm; ++$i)
        $j_day_no += self::$j_days_in_month[$i];

        $j_day_no += $jd;

        return ($j_day_no + 2) % 7 + 1;
    }

    // NO NEED
    function gregorian_to_jalali($g_y, $g_m, $g_d)
    {
        $gy = $g_y-1600;
        $gm = $g_m-1;
        $gd = $g_d-1;

        $g_day_no = 365*$gy+self::div($gy+3,4)-self::div($gy+99,100)+self::div($gy+399,400);

        for ($i=0; $i < $gm; ++$i)
        $g_day_no += self::$g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
        /* leap and after Feb */
        ++$g_day_no;
        $g_day_no += $gd;

        $j_day_no = $g_day_no-79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no %= 12053;

        $jy = 979+33*$j_np+4*self::div($j_day_no,1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no-1, 365);
            $j_day_no = ($j_day_no-1)%365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= self::$j_days_in_month[$i]; ++$i) {
            $j_day_no -= self::$j_days_in_month[$i];
        }
        $jm = $i+1;
        $jd = $j_day_no+1;


        return array($jy, $jm, $jd);
    }
    ////////////////////////////////////////////////////////////////////////////
    
} }