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
}
