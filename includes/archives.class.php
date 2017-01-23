<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateArchives extends gPersianDateModuleCore
{
	// FIXME: REWRITE THIS
	public static function get( $r = '' )
	{
		global $wpdb, $wp_locale;

		$defaults = array(
			'type'            => 'monthly',
			'limit'           => '',
			'format'          => 'html',
			'before'          => '',
			'after'           => '',
			'show_post_count' => FALSE,
			'echo'            => 1,
			'order'           => 'DESC',
			'post_type'       => 'post',
		);

		$args = wp_parse_args( $r, $defaults );

		$post_type_object = get_post_type_object( $args['post_type'] );

		if ( ! is_post_type_viewable( $post_type_object ) )
			return;

		$args['post_type'] = $post_type_object->name;

		if ( '' == $args['type'] )
			$args['type'] = 'monthly';

		if ( ! empty( $args['limit'] ) ) {
			$args['limit'] = absint( $args['limit'] );
			$args['limit'] = ' LIMIT '.$args['limit'];
		}

		$args['order'] = strtoupper( $args['order'] );
		if ( $args['order'] !== 'ASC' )
			$args['order'] = 'DESC';

		// this is what will separate dates on weekly archive links
		$archive_week_separator = '&#8211;';

		// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
		$archive_date_format_over_ride = 0;

		// options for daily/weekly archive (only if you over-ride the general date format)
		$archive_day_date_format = $archive_week_start_date_format = $archive_week_end_date_format = 'Y/m/d';

		if ( ! $archive_date_format_over_ride )
			$archive_day_date_format = $archive_week_start_date_format = $archive_week_end_date_format = get_option( 'date_format' );

		$sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $args['post_type'] );

		$where = apply_filters( 'getarchives_where', $sql_where, $args );
		$join  = apply_filters( 'getarchives_join', '', $args );

		$where = gPersianDateLinks::stripDateClauses( $where ); // just in case!

		$days_in_month     = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$output            = '';
		$last_persian_year = $last_persian_month = false;
		$afterafter        = $args['after'];
		$limit             = 1;

		$last_changed = wp_cache_get( 'last_changed', 'posts' );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'posts' );
		}

		if ( 'monthly' == $args['type'] ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `day` FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAY(post_date) ORDER BY post_date ".$args['order']; //.' '.$args['limit'];
			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( 0 == $result->year )
							continue;

						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_persian_month = gPersianDateDate::to( 'Ym', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );

						if ( $last_persian_month != $the_persian_month ) {
							$the_year = gPersianDateDate::to( 'Y', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
							$the_month = gPersianDateDate::to( 'm', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
							$url = get_month_link( $the_year, $the_month );
							$text = sprintf( _x( '%1$s %2$s', 'wp_get_archives monthly', GPERSIANDATE_TEXTDOMAIN ),
								gPersianDateStrings::month( $the_month ),
								gPersianDateTranslate::numbers( $the_year )
							);
							if ( $args['show_post_count'] ) {
								// $first_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/'.$the_month.'/'.'01' ) );
								// $last_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/'.$the_month.'/'.gPersianDateHelper::j_last_day_of_month( $the_month ) ) );
								$first_day = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, $the_month, 1, $the_year ) );
								$last_day = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, $the_month, $days_in_month[$the_month-1], $the_year ) );
								$post_count = $wpdb->get_results( "SELECT COUNT(id) as 'post_count' FROM $wpdb->posts $join $where AND post_date >='$first_day' AND post_date <='$last_day' ");
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives monthly count', GPERSIANDATE_TEXTDOMAIN ),
									gPersianDateTranslate::numbers( $post_count[0]->post_count ) ).$afterafter;
							}
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
							if ( $limit == $args['limit'] )
								break;
							$limit++;
						}
						$last_persian_month = $the_persian_month;
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );
				}
			} else {
				$output .= $results;
			}
		} elseif ( 'yearly' == $args['type'] ) {
			$query = "SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `dayofmonth` FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date ".$args['order'];;
			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( 0 == $result->year )
							continue;
						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_persian_year = gPersianDateDate::to( 'Y', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
						if ( $last_persian_year != $the_persian_year ) {
							$the_year = gPersianDateDate::to( 'Y', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
							$url = get_year_link( $the_year );
							$text = sprintf( _x( '%s', 'wp_get_archives yearly', GPERSIANDATE_TEXTDOMAIN ),
								gPersianDateTranslate::numbers( $the_year )
							);
							if ( $args['show_post_count'] ) {
								//$first_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/01/01' ) );
								//$last_day = date( 'Y-m-d', gPersianDateHelper::convert_back( ($the_year+1).'/01/01' ) );
								$first_day = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, 1, 1, $the_year ) );
								$last_day = date( 'Y-m-d H:i:s', gPersianDateDate::make( 0, 0, 0, 1, 1, $the_year+1 ) );
								$post_count = $wpdb->get_results( "SELECT COUNT(id) as 'post_count' FROM $wpdb->posts $join $where AND post_date >='$first_day' AND post_date <='$last_day' ");
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives yearly count', GPERSIANDATE_TEXTDOMAIN ),
									gPersianDateTranslate::numbers( $post_count[0]->post_count ) ).$afterafter;
							}
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
							if ( $limit == $args['limit'] )
								break;
							$limit++;
						}
						$last_persian_year = $the_persian_year;
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );
				}
			} else {
				$output .= $results;
			}
		} elseif ( 'daily' == $args['type'] ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				//$cache[ $key ] = $results;
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_year = gPersianDateDate::to( 'Y', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
						$the_month = gPersianDateDate::to( 'm', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
						$the_day = gPersianDateDate::to( 'd', $the_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE );
						$url = get_day_link( $the_year, $the_month, $the_day );
						$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
						$text = mysql2date( $archive_day_date_format, $date, true ); // this will convert the date
						if ( $args['show_post_count'] )
							$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives daily count', GPERSIANDATE_TEXTDOMAIN ),
								gPersianDateTranslate::numbers( $result->posts ) ).$afterafter;
						$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );
				}
			} else {
				$output .= $results;
			}
		} elseif ( 'weekly' == $args['type'] ) {
			$week = _wp_mysql_week( '`post_date`' );
			$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );

				$arc_w_last = '';
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( $result->week != $arc_w_last ) {
							$arc_year = $result->yr;
							$arc_w_last = $result->week;
							$arc_week = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
							$arc_week_start = date_i18n( $archive_week_start_date_format, $arc_week['start'] );
							$arc_week_end = date_i18n( $archive_week_end_date_format, $arc_week['end'] );
							$url = sprintf( '%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $result->week );
							$text = $arc_week_start.$archive_week_separator.$arc_week_end;
							if ( $args['show_post_count'] )
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives weekly count', GPERSIANDATE_TEXTDOMAIN ),
									gPersianDateTranslate::numbers( $result->posts ) ).$afterafter;
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
						}
					}
				}
				$results = $output;
				wp_cache_set( $key, $results, 'posts' );
			} else {
				$output .= $results;
			}
		} elseif ( ( 'postbypost' == $args['type'] ) || ( 'alpha' == $args['type'] ) ) {
			$orderby = ('alpha' == $type) ? 'post_title ASC ' : 'post_date DESC ';
			$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );

				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( $result->post_date != '0000-00-00 00:00:00' ) {
							$url = get_permalink( $result );
							if ( $result->post_title )
								$text = strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) );
							else
								$text = $result->ID;
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
						}
					}
				}
				$results = $output;
				wp_cache_set( $key, $results, 'posts' );
			} else {
				$output .= $results;
			}
		}

		if ( $args['echo'] )
			echo $output;
		else
			return $output;

	}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/// INSPIRED BY: Compact Archives
/// by Syed Balkhi & Noumaan Yaqoob
// @REF: https://wordpress.org/plugins/compact-archives/
// @REF: http://www.wpbeginner.com/plugins/how-to-create-compact-archives-in-wordpress/

	// LAST EDITED: 1/23/2017, 5:00:27 PM
	public static function getCompact( $atts = array() )
	{
		$args = self::atts( array(
			'post_type'      => 'post', // or array of types
			'post_author'    => 0, // all
			'css_class'      => 'table table-condensed', // Bootstrap 3 classes
			'month_name'     => TRUE, // FALSE to number
			'string_caption' => FALSE, // table caption
			'string_count'   => _x( '%s Posts', 'Archives: Compact', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
			'string_empty'   => _x( 'Archives are empty.', 'Archives: Compact', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
		), $atts );

		list( $first, $last ) = gPersianDateDate::getPosttypeFirstAndLast( $args['post_type'], array(), $args['post_author'], FALSE );

		if ( ! $first )
			return $args['string_empty'] ? '<span class="-empty">'.$args['string_empty'].'</span>' : FALSE;

		global $wpdb;

		$html   = '';
		$author = $args['post_author'] ? $wpdb->prepare( "AND post_author = %d", $args['post_author'] ) : '';

		if ( ! is_array( $args['post_type'] ) ) {

			$where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish' AND post_password = ''", $args['post_type'] );

		} else {

			$post_types_in = implode( ',', array_map( function( $v ){
				return "'".esc_sql( $v )."'";
			}, $args['post_type'] ) );

			$where = "WHERE post_type IN ( {$post_types_in} ) AND post_status = 'publish' AND post_password = ''";
		}

		$year = gPersianDateDate::_to( 'Y', $first );
		$now  = gPersianDateDate::_to( 'Y', $last );

		while ( $now >= $year ) {

			$html .= '<tr><td class="-year text-info text-right" style="width:10%;">';
				$html .= gPersianDateHTML::link( gPersianDateTranslate::numbers( $year ), get_year_link( $year ) );
			$html .= '</td>';

			for ( $month = 1; $month <= 12; $month += 1 ) {

				list( $first_day, $last_day ) = gPersianDateDate::monthFirstAndLast( $year, $month );

				$count = $wpdb->get_var( "
					SELECT COUNT(*)
					FROM {$wpdb->posts}
					{$where}
					{$author}
					AND post_date >= '{$first_day}'
					AND post_date <= '{$last_day}'
				" );

				$name = $args['month_name'] ? gPersianDateStrings::month( $month ) : gPersianDateTranslate::numbers( $month );

				if ( ! $count ) {
					$html .= '<td class="-month -empty text-muted text-center" style="width:7.5%;">'.$name.'</td>';
				} else {
					$title  = $args['string_count'] ? 'title="'.esc_attr( sprintf( $args['string_count'], gPersianDateTranslate::numbers( $count ) ) ).'"' : '';
					$html  .= '<td class="-month text-center" '.$title.' style="width:7.5%;">'.gPersianDateHTML::link( $name, get_month_link( $year, $month ) ).'</td>';
				}
			}

			$html .= '</tr>';
			$year++;
		}

		$table = '<div class="table-responsive"><table';

		if ( ! $args['month_name'] )
			$table .= ' dir="ltr"';

		$table .= ' class="date-archives-compact '.$args['css_class'].'">';

		if ( $args['string_caption'] )
			$table .= '<caption>'.$args['string_caption'].'</caption>';

		return $table.'<tbody>'.$html.'</tbody></table></div>';
	}
}
