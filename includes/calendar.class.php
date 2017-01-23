<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCalendar extends gPersianDateModuleCore
{

	public static function build( $initial = TRUE )
	{
		global $wpdb, $wp_locale, $m, $monthnum, $year;

		// error_log( print_r( compact( 'm', 'monthnum', 'year' ), TRUE ) );

		$current_time       = current_time( 'mysql' );
		$week_begins        = '6'; // week start on Saturday
		$ak_title_separator = ', ';

		$jcurrent_year  = $jthisyear  = gPersianDateDate::_to( 'Y', $current_time );
		$jcurrent_month = $jthismonth = gPersianDateDate::_to( 'm', $current_time );
		$jcurrent_day   = $jthisday   = gPersianDateDate::_to( 'd', $current_time );

		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$jthismonth = ''.zeroise( intval( $monthnum ), 2 );
			$jthisyear = ''.intval( $year );

		} elseif ( ! empty( $m ) ) {
			$jthisyear = ''.intval( substr( $m, 0, 4 ) );
			if ( strlen( $m ) < 6 )
				$jthismonth = '01';
			else
				$jthismonth = ''.zeroise( intval( substr( $m, 4, 2 ) ), 2 );
		}

		$jlast_day  = gPersianDateDate::_to( 't', gPersianDateDate::makeMySQL( 0, 0, 0, $jthismonth, 1, $jthisyear ) );
		$junixmonth = gPersianDateDate::make( 0, 0, 0, $jthismonth, 1, $jthisyear );

		$jfirst_day_mysql = date( 'Y-m-d H:i:s', $junixmonth );
		$jlast_day_mysql  = gPersianDateDate::makeMySQL( 23, 59, 59, $jthismonth, $jlast_day, $jthisyear );

		// get the next and previous month and year with at least one post
		$previous = $wpdb->get_row("SELECT post_date, MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date < '$jfirst_day_mysql'
			AND post_type = 'post' AND post_status = 'publish'
				ORDER BY post_date DESC
				LIMIT 1");

		$next = $wpdb->get_row("SELECT post_date, MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date > '$jlast_day_mysql'
			AND post_type = 'post' AND post_status = 'publish'
				ORDER BY post_date ASC
				LIMIT 1");

		/* translators: Calendar caption: 1: month name, 2: 4-digit year */
		$calendar_caption = _x( '%1$s %2$s', 'calendar caption', GPERSIANDATE_TEXTDOMAIN );
		$calendar_output = '<table id="wp-calendar"><caption>'
			.sprintf( $calendar_caption, gPersianDateStrings::month( $jthismonth ), gPersianDateTranslate::numbers( $jthisyear ) )
			.'</caption><thead><tr>';

		// $myweek = array();
		//
		// for ( $wdcount = 0; $wdcount <= 6; $wdcount++ )
		// 	$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		//
		// foreach ( $myweek as $wd ) {
		// 	$day_name = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
		// 	$wd = esc_attr( $wd );
		// 	$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
		// }

		$myweek = gPersianDateStrings::dayoftheweek( NULL, TRUE );
		$mydays = gPersianDateStrings::dayoftheweek( NULL, TRUE, NULL, TRUE );

		foreach ( $mydays as $wd => $day_initial )
			$calendar_output .= '<th scope="col" title="'.esc_attr( $myweek[$wd] ).'">'.$day_initial.'</th>';

		$calendar_output .= '</tr></thead><tfoot><tr>';

		if ( $previous ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="'
				.get_month_link( $previous->year, $previous->month )
				.'">&laquo; '
				.gPersianDateDate::to( 'M', $previous->post_date )
				.'</a></td>';
		} else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
		}

		$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

		if ( $next ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="'
			.get_month_link($next->year, $next->month)
			.'">'
			.gPersianDateDate::to( 'M', $next->post_date )
			.' &raquo;</a></td>';
		} else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
		}

		$calendar_output .= '</tr></tfoot><tbody><tr>';

		$daywithpost = array();

		// Get days with posts
		$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
			FROM $wpdb->posts WHERE post_date >= '$jfirst_day_mysql'
			AND post_type = 'post' AND post_status = 'publish'
			AND post_date <= '$jlast_day_mysql'", ARRAY_N );

		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[] = $daywith[0];
			}
		}

		$ak_titles_for_day = $daywithpostfull = array();

		$ak_post_titles = $wpdb->get_results("SELECT ID, post_title, post_date, DAYOFMONTH(post_date) as dom "
			."FROM $wpdb->posts "
			."WHERE post_date >= '$jfirst_day_mysql' "
			."AND post_date <= '$jlast_day_mysql' "
			."AND post_type = 'post' AND post_status = 'publish'"
		);

		if ( $ak_post_titles ) {
			foreach ( (array) $ak_post_titles as $ak_post_title ) {
				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator.$post_title;

				$daywithpostfull["$ak_post_title->dom"] = $ak_post_title->post_date;
			}
		}

		$jdaywithpost = array();

		foreach ( $daywithpostfull as $day => $post_date ) {
			$jday = gPersianDateDate::_to( 'j',  $post_date );
			$jdaywithpost[$jday] = $day;
		}

		// See how much we should pad in the beginning
		$pad = self::week_mod( date( 'w', $junixmonth ) - $week_begins );
		if ( 0 != $pad )
			$calendar_output .= "\n\t\t".'<td colspan="'.esc_attr( $pad ).'" class="pad">&nbsp;</td>';

		// first day of this month
		$jday = gPersianDateDate::_to( 'j', $jfirst_day_mysql );

		$jdaysinmonth = intval( $jlast_day );

		for ( $jday = 1; $jday <= $jdaysinmonth; ++$jday ) {

			if ( isset( $newrow ) && $newrow )
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			$newrow = FALSE;

			if ( $jday == $jcurrent_day
				&& $jthismonth == $jcurrent_month
				&& $jthisyear == $jcurrent_year )
					$calendar_output .= '<td id="today">';
			else
				$calendar_output .= '<td>';

			// any posts today?
			if ( array_key_exists( $jday, $jdaywithpost ) ) {
				// $this_time = strtotime( $daywithpostfull[$jdaywithpost[$jday]] );
				$calendar_output .= '<a href="'
					// .get_day_link( date( 'Y', $this_time ), date( 'm', $this_time ), $jdaywithpost[$jday] )
					.gPersianDateLinks::build( 'day', $jthisyear, $jthismonth, $jday )
					.'" title="'.esc_attr( $ak_titles_for_day[ $jdaywithpost[$jday] ] )
					.'">'.gPersianDateTranslate::numbers( $jday ).'</a>';
			} else {
				$calendar_output .= gPersianDateTranslate::numbers( $jday );
			}

			$calendar_output .= '</td>';

			if ( 6 == self::week_mod( date( 'w', gPersianDateDate::make( 0, 0, 0, $jthismonth, $jday, $jthisyear ) ) - $week_begins ) )
				$newrow = TRUE;
		}

		$pad = 7 - self::week_mod( date( 'w', gPersianDateDate::make( 0, 0, 0, $jthismonth, $jday, $jthisyear ) ) - $week_begins );

		if ( $pad != 0 && $pad != 7 )
			$calendar_output .= "\n\t\t".'<td class="pad" colspan="'.esc_attr( $pad ).'">&nbsp;</td>';


		$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";
		return $calendar_output;
	}

	// Get number of days since the start of the week.
	// exact copy of core calendar_week_mod()
	public static function week_mod( $num )
	{
		$base = 7;
		return ( $num - $base * floor( $num / $base ) );
	}

	public static function get( $initial = TRUE, $echo = TRUE )
	{
		global $wpdb, $m, $monthnum, $year, $posts;

		$key = md5( $m.$monthnum.$year );

		if ( $cache = wp_cache_get( 'get_calendar', 'calendar' ) ) {
			if ( is_array( $cache ) && isset( $cache[ $key ] ) ) {

				$output = apply_filters( 'get_calendar', $cache[$key] );

				if ( ! $echo )
					return $output;

				echo $output;
				return;
			}
		}

		if ( ! is_array( $cache ) )
			$cache = array();

		// quick check: if we have no posts at all, abort!
		if ( ! $posts ) {
			$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 1");
			if ( ! $gotsome ) {
				$cache[$key] = '';
				wp_cache_set( 'get_calendar', $cache, 'calendar' );
				return;
			}
		}

		$cache[$key] = self::build( $initial );
		wp_cache_set( 'get_calendar', $cache, 'calendar' );

		$output = apply_filters( 'get_calendar', $cache[$key] );

		if ( ! $echo )
			return $output;

		echo $output;
	}
}
