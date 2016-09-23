<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDate extends gPersianDateModuleCore
{

	public static function to( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = GPERSIANDATE_LOCALE, $translate = TRUE )
	{
		if ( is_null( $time ) )
			$time = current_time( 'mysql' );

		if ( gPersianDateFormat::checkISO( $format ) )
			return mysql2date( $format, $time, FALSE );

		$string = gPersianDateDateTime::to( $time, $format, $timezone );

		if ( $translate )
			return gPersianDateTranslate::numbers( $string, $locale );

		return $string;
	}

	public static function toHijri( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = 'ar', $translate = TRUE )
	{
		if ( is_null( $time ) )
			$time = current_time( 'mysql' );

		if ( gPersianDateFormat::checkISO( $format ) )
			return mysql2date( $format, $time, FALSE );

		$string = gPersianDateDateTime::to( $time, $format, $timezone, 'Hijri' );

		if ( $translate )
			return gPersianDateTranslate::numbers( $string, $locale );

		return $string;
	}

	public static function make( $hour, $minute, $second, $jmonth, $jday, $jyear )
	{
		list( $year, $month, $day ) = gPersianDateDateTime::fromJalali( $jyear, $jmonth, $jday );

		return mktime(
			(int) $hour,
			(int) $minute,
			(int) $second,
			(int) $month,
			(int) $day,
			(int) $year
		);
	}

	public static function monthFirstAndLast( $year, $month, $format = 'Y-m-d H:i:s' )
	{
		$days = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );

		return array(
			date( $format, self::make( 0, 0, 0, $month, 1, $year ) ),
			date( $format, self::make( 23, 59, 59, $month, $days[$month-1], $year ) ),
		);
	}

	public static function postDate( $post = NULL, $gmt = FALSE, $timestamp = FALSE )
	{
		$the_post = get_post( $post );

		$the_date = $gmt ? $the_post->post_date_gmt : $the_post->post_date;

		if ( ! $timestamp )
			return $the_date;

		return mysql2date( 'U', $the_date, FALSE );
	}

	public static function postModifiedDate( $post = NULL, $gmt = FALSE, $timestamp = FALSE )
	{
		$the_post = get_post( $post );

		$the_date = $gmt ? $the_post->post_modified_gmt : $the_post->post_modified;

		if ( ! $timestamp )
			return $the_date;

		return mysql2date( 'U', $the_date, FALSE );
	}

	public static function commentDate( $comment, $gmt = FALSE, $timestamp = FALSE )
	{
		$the_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;

		if ( ! $timestamp )
			return $the_date;

		return mysql2date( 'U', $the_date, FALSE );
	}

	public static function getPosttypeMonths( $post_type = 'post', $args = array(), $user_id = 0 )
	{
		global $wpdb;

		$author = $user_id ? $wpdb->prepare( "AND post_author = %d", $user_id ) : '';

		$extra_checks = "AND post_status != 'auto-draft'";

		if ( ! isset( $args['post_status'] )
			|| 'trash' !== $args['post_status'] )
				$extra_checks .= " AND post_status != 'trash'";

		else if ( isset( $args['post_status'] ) )
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $args['post_status'] );

		$query = $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month, DAY( post_date ) as day
			FROM $wpdb->posts
			WHERE post_type = %s
			{$author}
			{$extra_checks}
			ORDER BY post_date DESC
		", $post_type );

		$key = md5( $query );
		$cache = wp_cache_get( 'wp_get_archives' , 'general' );

		if ( ! isset( $cache[$key] ) ) {
			$months = $wpdb->get_results( $query );
			$cache[$key] = $months;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$months = $cache[$key];
		}

		$count = count( $months );
		if ( ! $count || ( 1 == $count && 0 == $months[0]->month ) )
			return FALSE;

		$list = array();
		$last = FALSE;

		foreach ( $months as $row ) {

			if ( 0 == $row->year )
				continue;

			$date  = mktime( 0 ,0 , 0, zeroise( $row->month, 2 ), $row->day, $row->year );
			$month = self::to( 'Ym', $date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );

			if ( $last != $month )
				$list[$month] = self::to( 'M Y', $date );

			$last = $month;
		}

		return $list;
	}
}
