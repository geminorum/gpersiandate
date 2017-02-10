<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateArchives extends gPersianDateModuleCore
{

	// FIXME: REWRITE THIS
	// REPLICA: `wp_get_archives()`
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

		$days_in_month     = gPersianDateDate::daysInMonth();
		$output            = '';
		$last_persian_year = $last_persian_month = FALSE;
		$afterafter        = $args['after'];
		$limit             = 1;

		if ( ! $last_changed = wp_cache_get( 'last_changed', 'posts' ) ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'posts' );
		}

		if ( 'monthly' == $args['type'] ) {

			$query = "
				SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `day`
				FROM {$wpdb->posts}
				{$join}
				{$where}
				GROUP BY YEAR(post_date), MONTH(post_date), DAY(post_date)
				ORDER BY post_date ".$args['order']; //.' '.$args['limit'];

			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";

			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {

				$results = $wpdb->get_results( $query );

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						if ( 0 == $result->year )
							continue;

						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_persian_month = gPersianDateDate::_to( 'Ym', $the_date );

						if ( $last_persian_month != $the_persian_month ) {

							$the_year  = gPersianDateDate::_to( 'Y', $the_date );
							$the_month = gPersianDateDate::_to( 'm', $the_date );

							$url = get_month_link( $the_year, $the_month );

							$text = sprintf( _x( '%1$s %2$s', 'wp_get_archives monthly', GPERSIANDATE_TEXTDOMAIN ),
								gPersianDateStrings::month( $the_month ),
								gPersianDateTranslate::numbers( $the_year )
							);

							if ( $args['show_post_count'] ) {

								list( $first_day, $last_day ) = gPersianDateDate::monthFirstAndLast( $the_year, $the_month );

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

		} else if ( 'yearly' == $args['type'] ) {

			$query = "
				SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `dayofmonth`
				FROM {$wpdb->posts}
				{$join}
				{$where}
				GROUP BY YEAR(post_date), MONTH(post_date)
				ORDER BY post_date ".$args['order'];

			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";

			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {

				$results = $wpdb->get_results( $query );

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						if ( 0 == $result->year )
							continue;

						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->dayofmonth, $result->year );
						$the_persian_year = gPersianDateDate::_to( 'Y', $the_date );

						if ( $last_persian_year != $the_persian_year ) {

							$the_year = gPersianDateDate::_to( 'Y', $the_date );
							$url      = get_year_link( $the_year );
							$text     = sprintf( _x( '%s', 'wp_get_archives yearly', GPERSIANDATE_TEXTDOMAIN ), gPersianDateTranslate::numbers( $the_year ) );

							if ( $args['show_post_count'] ) {

								$first_day = gPersianDateDate::makeMySQL( 0, 0, 0, 1, 1, $the_year );
								$last_day  = gPersianDateDate::makeMySQL( 0, 0, 0, 1, 1, $the_year + 1 ); // FIXME: is this correct last day of year?

								$post_count = $wpdb->get_results( "
									SELECT COUNT(id) as 'post_count'
									FROM {$wpdb->posts}
									{$join}
									{$where}
									AND post_date >='{$first_day}'
									AND post_date <='{$last_day}'
								" );

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

		} else if ( 'daily' == $args['type'] ) {

			$query = "
				SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts
				FROM {$wpdb->posts}
				{$join}
				{$where}
				GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date)
				ORDER BY post_date ".$args['order'].' '.$args['limit'];

			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";

			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {

				$results = $wpdb->get_results( $query );
				//$cache[ $key ] = $results;

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						$the_date  = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->dayofmonth, $result->year );
						$the_year  = gPersianDateDate::_to( 'Y', $the_date );
						$the_month = gPersianDateDate::_to( 'm', $the_date );
						$the_day   = gPersianDateDate::_to( 'd', $the_date );

						$url = get_day_link( $the_year, $the_month, $the_day );

						$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
						$text = mysql2date( $archive_day_date_format, $date, TRUE ); // this will convert the date

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

		} else if ( 'weekly' == $args['type'] ) {

			$week = _wp_mysql_week( '`post_date`' );

			$query = "
				SELECT DISTINCT {$week} AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts`
				FROM {$wpdb->posts}
				{$join}
				{$where}
				GROUP BY {$week}, YEAR( `post_date` )
				ORDER BY `post_date` ".$args['order'].' '.$args['limit'];

			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";

			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {

				$results = $wpdb->get_results( $query );

				$arc_w_last = '';

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						if ( $result->week != $arc_w_last ) {

							$arc_year       = $result->yr;
							$arc_w_last     = $result->week;
							$arc_week       = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
							$arc_week_start = date_i18n( $archive_week_start_date_format, $arc_week['start'] );
							$arc_week_end   = date_i18n( $archive_week_end_date_format, $arc_week['end'] );

							$url  = sprintf( '%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $result->week );
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

		} else if ( ( 'postbypost' == $args['type'] ) || ( 'alpha' == $args['type'] ) ) {

			$orderby = ('alpha' == $type) ? 'post_title ASC ' : 'post_date DESC ';

			$query = "
				SELECT *
				FROM {$wpdb->posts}
				{$join}
				{$where}
				ORDER BY ".$args['order'].' '.$args['limit'];

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
			'link_anchor'    => FALSE, // TRUE to link within the page
			'string_caption' => FALSE, // table caption
			'string_count'   => _x( '%s Posts', 'Archives: Compact', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
			'string_empty'   => _x( 'Archives are empty.', 'Archives: Compact', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
		), $atts );

		list( $first, $last ) = gPersianDateWordPress::getPosttypeFirstAndLast( $args['post_type'], array(), $args['post_author'], FALSE );

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
				if ( $args['link_anchor'] )
					$html .= gPersianDateTranslate::numbers( $year );
				else
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
					$link  = $args['link_anchor'] ? gPersianDateHTML::scroll( $name, $year.zeroise( $month, 2 ) ) : gPersianDateHTML::link( $name, get_month_link( $year, $month ) );
					$title = $args['string_count'] ? 'title="'.esc_attr( sprintf( $args['string_count'], gPersianDateTranslate::numbers( $count ) ) ).'"' : '';
					$html  .= '<td class="-month text-center" '.$title.' style="width:7.5%;">'.$link.'</td>';
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

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/// ADOPTED FROM: Clean My Archives v1.0.0 - 2017-01-23
/// by Justin Tadlock
// @REF: https://wordpress.org/plugins/clean-my-archives/
// @REF: https://github.com/justintadlock/clean-my-archives

	public static function getClean( $atts = array() )
	{
		$args = self::atts( array(
			'limit'         => -1,
			'year'          => '',
			'month'         => '',
			'post_type'     => 'post', // or array of types
			'post_author'   => 0, // all
			'comment_count' => FALSE,
			'row_context'   => FALSE,
			'row_day'       => FALSE,
			'css_class'     => 'table table-condensed', // Bootstrap 3 classes
			'string_empty'  => _x( 'Archives are empty.', 'Archives: Clean', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
			'string_count'  => _x( 'Post comment count', 'Archives: Clean', GPERSIANDATE_TEXTDOMAIN ), // FALSE to disable
		), $atts );

		// FIXME: must check year/month args for conversion

		$html  = $current_year = $current_month = $current_day = '';
		$query = array(

			'year'           => $args['year'] ? absint( $args['year'] ) : '',
			'monthnum'       => $args['month'] ? absint( $args['month'] ) : '',
			'post_type'      => is_array( $args['post_type'] ) ? $args['post_type'] : explode( ',', $args['post_type'] ),
			'author'         => $args['post_author'],
			'posts_per_page' => intval( $args['limit'] ),

			'ignore_sticky_posts'    => TRUE,
			'no_found_rows'          => TRUE,
			'update_post_meta_cache' => FALSE,
			'update_post_term_cache' => FALSE,
			'lazy_load_term_meta'    => FALSE,
		);

		$loop = new \WP_Query( $query );

		if ( $loop->have_posts() ) {

			while ( $loop->have_posts() ) {

				$loop->the_post();

				// we need this to compare it with the previous post date.
				$year   = get_the_time( 'Y' );
				$month  = get_the_time( 'm' );
				$daynum = get_the_time( 'd' );

				// if the current date doesn't match this post's date, we need extra formatting.
				if ( $current_year !== $year || $current_month !== $month ) {

					// close the list if this isn't the first post.
					if ( $current_month && $current_year )
						$html .= '</ul></div>';

					// set the current year and month to this post's year and month.
					$current_year  = $year;
					$current_month = $month;
					$current_day   = '';

					$number_year  = gPersianDateTranslate::numbers_back( $current_year );
					$number_month = gPersianDateTranslate::numbers_back( $current_month );

					// add a heading with the month and year and link it to the monthly archive.
					$html .= sprintf(
						'<div id="%s"><h3 class="-month"><a href="%s">%s</a></h3>',
						$number_year.zeroise( $number_month, 2 ),
						esc_url( gPersianDateLinks::build( 'month', $number_year, $number_month ) ),
						esc_html( get_the_time( _x( 'F Y', 'Archives: Clean', GPERSIANDATE_TEXTDOMAIN ) ) )
					);

					// open a new unordered list.
					$html .= '<ul class="list-unstyled">';
				}

				// get the post's day.
				$day = sprintf( '<span class="-day">%s</span>', get_the_time( esc_html_x( 'j:', 'Archives: Clean', GPERSIANDATE_TEXTDOMAIN ) ) );

				// check if there's a duplicate day so we can add a class.
				$duplicate_day = $current_day && $daynum === $current_day ? ' class="-day-duplicate"' : '';
				$current_day   = $daynum;

				if ( $args['row_context'] ) {

					if ( $args['row_day'] )
						printf( '<li%s>%s ', $duplicate_day, $day );
					else
						printf( '<li%s>', $duplicate_day );

						get_template_part( 'row', $args['row_context'] );
					echo '</li>';

				} else {

					if ( $args['comment_count'] ) {
						$comments_num = sprintf( esc_html_x( '(%s)', 'Archives: Clean', GPERSIANDATE_TEXTDOMAIN ), get_comments_number() );
						$comments     = sprintf( '<small class="-comments" title="%s">%s</small>', esc_attr( $args['string_count'] ), gPersianDateTranslate::numbers( $comments_num ) );
					}

					// add the post list item to the formatted archives.
					$html .= the_title(
						sprintf( '<li%s>%s <a href="%s">', $duplicate_day, $day, esc_url( get_permalink() ) ),
						( $args['comment_count'] ? sprintf( '</a> %s</li>', $comments ) : '</a></li>' ),
						FALSE
					);
				}
			}

			// close the final unordered list
			$html .= '</ul></div>';

			wp_reset_postdata();

		} else if ( ! $args['string_empty'] ) {

			return FALSE;

		} else {

			$html = '<span class="-empty">'.$args['string_empty'].'</span>';
		}

		return sprintf( '<div class="date-archives-clean %s">%s</div>', $args['css_class'], $html );
	}
}
