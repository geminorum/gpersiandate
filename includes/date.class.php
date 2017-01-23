<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDate extends gPersianDateModuleCore
{

	public static function daysInMonth()
	{
		return array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
	}

	public static function to( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = GPERSIANDATE_LOCALE, $translate = TRUE, $calendar = 'Jalali' )
	{
		if ( FALSE === $time )
			return FALSE;

		if ( is_null( $time ) )
			$time = current_time( 'mysql' );

		if ( gPersianDateFormat::checkISO( $format ) )
			return mysql2date( $format, $time, FALSE );

		$string = gPersianDateDateTime::to( $time, $format, $timezone, $calendar );

		if ( $translate )
			return gPersianDateTranslate::numbers( $string, $locale );

		return $string;
	}

	// not translating!
	public static function _to( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = GPERSIANDATE_LOCALE, $translate = TRUE, $calendar = 'Jalali' )
	{
		return self::to( $format, $time, $timezone, $locale, FALSE, $calendar );
	}

	public static function toHijri( $format, $time = NULL, $timezone = GPERSIANDATE_TIMEZONE, $locale = 'ar', $translate = TRUE )
	{
		return self::to( $format, $time, $timezone, $locale, $translate, 'Hijri' );
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

	public static function makeMySQL( $hour, $minute, $second, $jmonth, $jday, $jyear )
	{
		return date( 'Y-m-d H:i:s', self::make( $hour, $minute, $second, $jmonth, $jday, $jyear ) );
	}

	public static function makeFromArray( $atts = array() )
	{
		$args = self::atts( array(
			'year'   => 1362, // ;)
			'month'  => 1,
			'day'    => 1,
			'hour'   => 0,
			'minute' => 0,
			'second' => 0,
		), $atts );

		return self::make( $args['hour'], $args['minute'], $args['second'], $args['month'], $args['day'], $args['year'] );
	}

	public static function makeMySQLFromArray( $atts = array() )
	{
		return date( 'Y-m-d H:i:s', self::makeFromArray( $atts ) );
	}

	public static function makeFromInput( $input )
	{
		// FIXME: needs sanity checks
		$parts = explode( '/', $input );

		return self::make( 0, 0, 0, $parts[1], $parts[2], $parts[0] );
	}

	public static function makeMySQLFromInput( $input )
	{
		return date( 'Y-m-d H:i:s', self::makeFromInput( $input ) );
	}

	public static function monthFirstAndLast( $year, $month, $format = 'Y-m-d H:i:s' )
	{
		$days = self::daysInMonth();

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
		if ( ! $the_post = get_post( $post ) )
			return FALSE;

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

	public static function getPosttypeFirstAndLast( $post_types = 'post', $args = array(), $user_id = 0, $protected = TRUE )
	{
		global $wpdb;

		if ( ! is_array( $post_types ) ) {

			$where = $wpdb->prepare( "WHERE post_type = %s", $post_types );

		} else {

			$post_types_in = implode( ',', array_map( function( $v ){
				return "'".esc_sql( $v )."'";
			}, $post_types ) );

			$where = "WHERE post_type IN ( {$post_types_in} )";
		}

		$author = $user_id ? $wpdb->prepare( "AND post_author = %d", $user_id ) : '';

		$extra_checks = "AND post_status != 'auto-draft'";

		if ( ! isset( $args['post_status'] )
			|| 'trash' !== $args['post_status'] )
				$extra_checks .= " AND post_status != 'trash'";

		else if ( isset( $args['post_status'] ) )
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $args['post_status'] );

		if ( ! $protected )
			$extra_checks .= " AND post_password = ''";

		$first = gPersianDateUtilities::getResultsDB( "
			SELECT post_date AS date
			FROM {$wpdb->posts}
			{$where}
			{$author}
			{$extra_checks}
			ORDER BY post_date ASC
			LIMIT 1
		" );

		$last = gPersianDateUtilities::getResultsDB( "
			SELECT post_date AS date
			FROM {$wpdb->posts}
			{$where}
			{$author}
			{$extra_checks}
			ORDER BY post_date DESC
			LIMIT 1
		" );

		return array(
			( count( $first ) ? $first[0]->date : '' ),
			( count( $last )  ? $last[0]->date  : '' ),
		);
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
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month, DAY( post_date ) AS day
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
			$month = self::_to( 'Ym', $date );

			if ( $last != $month )
				$list[$month] = self::to( 'M Y', $date );

			$last = $month;
		}

		return $list;
	}
}
