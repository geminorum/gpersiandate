<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateArchives extends gPersianDateModuleCore
{

	// TODO: REWRITE THIS
	// @SOURCE: `wp_get_archives()`
	public static function get( $r = '' )
	{
		global $wpdb;

		$defaults = [
			'type'            => 'monthly',
			'limit'           => '',
			'format'          => 'html',
			'before'          => '',
			'after'           => '',
			'show_post_count' => FALSE,
			'echo'            => 1,
			'order'           => 'DESC',
			'post_type'       => 'post',
		];

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

		$output            = '';
		$last_persian_year = $last_persian_month = FALSE;
		$afterafter        = $args['after'];
		$limit             = 1;

		if ( ! $last_changed = wp_cache_get( 'last_changed', 'posts' ) ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'posts' );
		}

		if ( 'monthly' == $args['type'] ) {

			// FIXME: use: gPersianDateWordPress::getPostTypeMonths()

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

						$datetime          = gPersianDateDate::toObject( mktime( 0, 0, 0, $result->month, $result->day, $result->year ) );
						$the_persian_month = gPersianDateDate::_fromObject( 'Ym', $datetime );

						if ( $last_persian_month != $the_persian_month ) {

							$the_year  = gPersianDateDate::_fromObject( 'Y', $datetime );
							$the_month = gPersianDateDate::_fromObject( 'm', $datetime );

							$url = get_month_link( $the_year, $the_month );

							/* translators: %1$s: month name, %2$s: year number */
							$text = sprintf( _x( '%1$s %2$s', 'wp_get_archives monthly', 'gpersiandate' ),
								gPersianDateStrings::month( $the_month ),
								gPersianDateTranslate::numbers( $the_year )
							);

							if ( $args['show_post_count'] ) {

								list( $first_day, $last_day ) = gPersianDateDate::monthFirstAndLast( $the_year, $the_month );

								$post_count = $wpdb->get_results( "SELECT COUNT(id) as 'post_count' FROM $wpdb->posts $join $where AND post_date >='$first_day' AND post_date <='$last_day' ");

								/* translators: %s: monthly count */
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives monthly count', 'gpersiandate' ),
									gPersianDateTranslate::numbers( $post_count[0]->post_count ) ).$afterafter;
							}

							$output.= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );

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

				$output.= $results;
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

						$datetime         = gPersianDateDate::toObject( mktime( 0, 0, 0, $result->month, $result->dayofmonth, $result->year ) );
						$the_persian_year = gPersianDateDate::_fromObject( 'Y', $datetime );

						if ( $last_persian_year != $the_persian_year ) {

							$the_year = gPersianDateDate::_fromObject( 'Y', $datetime );
							$url      = get_year_link( $the_year );
							/* translators: %s: yearly */
							$text     = sprintf( _x( '<span>%s</span>', 'wp_get_archives yearly', 'gpersiandate' ), gPersianDateTranslate::numbers( $the_year ) );

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

								/* translators: %s: yearly count */
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives yearly count', 'gpersiandate' ),
									gPersianDateTranslate::numbers( $post_count[0]->post_count ) ).$afterafter;
							}

							$output.= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );

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

				$output.= $results;
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

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						$datetime  = gPersianDateDate::toObject( mktime( 0, 0, 0, $result->month, $result->dayofmonth, $result->year ) );
						$the_year  = gPersianDateDate::_fromObject( 'Y', $datetime );
						$the_month = gPersianDateDate::_fromObject( 'm', $datetime );
						$the_day   = gPersianDateDate::_fromObject( 'd', $datetime );

						$url = get_day_link( $the_year, $the_month, $the_day );

						$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
						$text = mysql2date( $archive_day_date_format, $date, TRUE ); // this will convert the date

						if ( $args['show_post_count'] )
							/* translators: %s: daily count */
							$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives daily count', 'gpersiandate' ),
								gPersianDateTranslate::numbers( $result->posts ) ).$afterafter;

						$output.= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
					}

					$results = $output;

					wp_cache_set( $key, $results, 'posts' );
				}

			} else {

				$output.= $results;
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
								/* translators: %s: weekly count */
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives weekly count', 'gpersiandate' ),
									gPersianDateTranslate::numbers( $result->posts ) ).$afterafter;

							$output.= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
						}
					}
				}

				$results = $output;

				wp_cache_set( $key, $results, 'posts' );

			} else {

				$output.= $results;
			}

		} else if ( ( 'postbypost' == $args['type'] ) || ( 'alpha' == $args['type'] ) ) {

			$orderby = 'alpha' == $args['type'] ? 'post_title ASC ' : 'post_date DESC ';

			$query = "
				SELECT *
				FROM {$wpdb->posts}
				{$join}
				{$where}
				ORDER BY {$orderby} ".$args['order'].' '.$args['limit'];

			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";

			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {

				$results = $wpdb->get_results( $query );

				if ( $results ) {

					foreach ( (array) $results as $result ) {

						if ( $result->post_date != '0000-00-00 00:00:00' ) {

							$title = $result->post_title
								? strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) )
								: $result->ID;

							$output.= get_archives_link( get_permalink( $result ), $title, $args['format'], $args['before'], $args['after'] );
						}
					}
				}

				$results = $output;

				wp_cache_set( $key, $results, 'posts' );

			} else {

				$output.= $results;
			}
		}

		if ( ! $args['echo'] )
			return $output;

		echo $output;
	}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/// INSPIRED BY: Compact Archives
/// by Syed Balkhi & Noumaan Yaqoob
// @REF: https://wordpress.org/plugins/compact-archives/
// @REF: http://www.wpbeginner.com/plugins/how-to-create-compact-archives-in-wordpress/

	public static function getCompact( $atts = [] )
	{
		global $wpdb;

		$args = self::atts( [
			'post_type'      => 'post', // or array of types
			'post_author'    => 0, // all
			'css_class'      => 'table table-condensed', // Bootstrap 3 classes
			'month_name'     => TRUE, // FALSE to number
			'link_anchor'    => FALSE, // TRUE to link within the page
			'string_caption' => FALSE, // table caption
			/* translators: %s: posts count */
			'string_count'   => _x( '%s Posts', 'Archives: Compact', 'gpersiandate' ), // FALSE to disable
			'string_empty'   => _x( 'Archives are empty.', 'Archives: Compact', 'gpersiandate' ), // FALSE to disable
		], $atts );

		list( $first, $last ) = gPersianDateWordPress::getPosttypeFirstAndLastObject( $args['post_type'], [], $args['post_author'], FALSE );

		if ( ! $first )
			return $args['string_empty'] ? '<span class="-empty">'.$args['string_empty'].'</span>' : FALSE;

		$html = '';

		$where = is_array( $args['post_type'] )
			? "WHERE post_type IN ( '".implode( "', '", esc_sql( $args['post_type'] ) )."' )"
			: $wpdb->prepare( "WHERE post_type = %s", $args['post_type'] );

		$author = $args['post_author']
			? $wpdb->prepare( "AND post_author = %d", $args['post_author'] )
			: '';

		$year = gPersianDateDate::_fromObject( 'Y', $first );
		$now  = gPersianDateDate::_fromObject( 'Y', $last );

		while ( $now >= $year ) {

			$html.= '<tr><td class="-year">';

			$html.= $args['link_anchor']
				? gPersianDateTranslate::numbers( $year )
				: gPersianDateHTML::link( gPersianDateTranslate::numbers( $year ), get_year_link( $year ) );

			$html.= '</td>';

			for ( $month = 1; $month <= 12; $month += 1 ) {

				list( $first_day, $last_day ) = gPersianDateDate::monthFirstAndLast( $year, $month );

				$count = $wpdb->get_var( "
					SELECT COUNT(*)
					FROM {$wpdb->posts}
					{$where}
					{$author}
					AND post_status = 'publish'
					AND post_password = ''
					AND post_date >= '{$first_day}'
					AND post_date <= '{$last_day}'
				" );

				$name = $args['month_name']
					? gPersianDateStrings::month( $month )
					: gPersianDateTranslate::numbers( $month );

				if ( $count ) {

					$link  = $args['link_anchor']
						? gPersianDateHTML::scroll( $name, $year.zeroise( $month, 2 ) )
						: gPersianDateHTML::link( $name, get_month_link( $year, $month ) );

					$title = $args['string_count']
						? 'title="'.esc_attr( sprintf( $args['string_count'], gPersianDateTranslate::numbers( $count ) ) ).'"'
						: '';

					$html.= '<td class="-month" '.$title.'>'.$link.'</td>';

				} else {

					$html.= '<td class="-month -empty">'.$name.'</td>';
				}
			}

			$html.= '</tr>';
			$year++;
		}

		$table = '<div class="-wrap table-responsive"><table';

		if ( ! $args['month_name'] )
			$table.= ' dir="ltr"';

		$table.= ' class="date-archives-compact '.$args['css_class'].'">';

		if ( $args['string_caption'] )
			$table.= '<caption>'.$args['string_caption'].'</caption>';

		return $table.'<tbody>'.$html.'</tbody></table></div>';
	}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/// ADOPTED FROM: Clean My Archives v1.2.0 - 2017-10-20
/// by Justin Tadlock
// @REF: https://wordpress.org/plugins/clean-my-archives/
// @REF: https://github.com/justintadlock/clean-my-archives

	public static function getClean( $atts = [] )
	{
		$args = self::atts( [
			'limit'             => -1,
			'order'             => 'DESC',
			'year'              => '',
			'month'             => '',
			'post_type'         => 'post', // or array of types
			'post_author'       => 0, // all
			'comment_count'     => FALSE,
			'row_context'       => FALSE,
			'row_day'           => TRUE,
			'css_class'         => '',
			'format_month_year' => _x( 'F Y', 'Archives: Clean: Month + Year Datetime Format', 'gpersiandate' ),
			'format_post_date'  => _x( 'j', 'Archives: Clean: Day Datetime Format', 'gpersiandate' ),
			'string_empty'      => _x( 'Archives are empty.', 'Archives: Clean', 'gpersiandate' ), // FALSE to disable
			'string_count'      => _x( 'Post comment count', 'Archives: Clean', 'gpersiandate' ), // FALSE to disable
		], $atts );

		$query = [
			'year'           => $args['year'] ? absint( $args['year'] ) : '',
			'monthnum'       => $args['month'] ? absint( $args['month'] ) : '',
			'post_type'      => is_array( $args['post_type'] ) ? $args['post_type'] : explode( ',', $args['post_type'] ),
			'author'         => $args['post_author'],
			'posts_per_page' => intval( $args['limit'] ),
			'order'          => in_array( $args['order'], [ 'ASC', 'DESC' ] ) ? $args['order'] : 'DESC',

			'no_found_rows'          => TRUE,
			'ignore_sticky_posts'    => TRUE,
			'update_post_meta_cache' => FALSE,
			'update_post_term_cache' => FALSE,
			'lazy_load_term_meta'    => FALSE,
		];

		// if we have one specific post type,
		// let's get the query args to append to the month link
		$link_query = 1 === count( $query['post_type'] )
			&& 'post' !== $query['post_type'][0]
				? [ 'post_type' => $query['post_type'][0] ]
				: FALSE;

		// validate boolean values passed through shortcode
		$show_comments = self::validateBoolean( $args['comment_count'] ) ? 1 : FALSE;

		$html = $current_year = $current_month = $current_day = '';

		$loop = new \WP_Query( $query );

		if ( $loop->have_posts() ) {

			while ( $loop->have_posts() ) {

				$loop->the_post();
				$post = get_post();

				// we need this to compare it with the previous post date
				// TODO: get all at once then split
				$year   = get_the_time( 'Y', $post );
				$month  = get_the_time( 'm', $post );
				$daynum = get_the_time( 'd', $post );

				// if the current date doesn't match this post's date, we need extra formatting
				if ( $current_year !== $year || $current_month !== $month ) {

					// close the list if this isn't the first post
					if ( $current_month && $current_year )
						$html .= '</dl><div class="clearfix"></div></li>';

					// set the current year and month to this post's year and month
					$current_year  = $year;
					$current_month = $month;
					$current_day   = '';

					$number_year  = gPersianDateTranslate::numbers_back( $current_year );
					$number_month = gPersianDateTranslate::numbers_back( $current_month );
					$link_month   = gPersianDateLinks::build( 'month', $number_year, $number_month );

					if ( $link_query )
						$link_month = add_query_arg( $link_query, $link_month );

					// add a heading with the month and year and link it to the monthly archive
					$html.= sprintf(
						'<li id="%s"><h4 class="-month"><a href="%s">%s</a></h4>',
						$number_year.zeroise( $number_month, 2 ),
						esc_url( $link_month ),
						esc_html( get_the_time( $args['format_month_year'], $post ) )
					);

					$html.= '<dl class="dl-horizontal">';
				}

				// check if there's a duplicate day so we can add a class
				$duplicate_day = $current_day && $daynum === $current_day ? ' class="-day-title -day-duplicate"' : ' class="-day-title"';
				$current_day   = $daynum;

				if ( $args['row_day'] ) {

					$number_day = gPersianDateTranslate::numbers_back( $current_day );
					$link_day   = gPersianDateLinks::build( 'day', $number_year, $number_month, $number_day );

					if ( $link_query )
						$link_day = add_query_arg( $link_query, $link_day );

					$day = vsprintf( '<dt%s><a href="%s" class="-day">%s</a></dt>', [
						$duplicate_day,
						esc_url( $link_day ),
						get_the_time( $args['format_post_date'], $post ),
					] );

				} else {

					$day = '';
				}

				if ( $args['row_context'] ) {

					ob_start();

					echo $day;

					echo '<dd>';
						get_template_part( 'row', $args['row_context'] );
					echo '</dd>';

					$html.= ob_get_clean();

				} else {

					if ( trim( $post->post_title ) )
						$title = gPersianDateUtilities::prepTitle( $post->post_title, $post->ID );
					else
						$title = _x( '[Untitled]', 'Archives: Clean: Untitled Post', 'gpersiandate' );

					$template = '%s<dd><a href="%s" rel="bookmark">%s</a></dd>';
					$values   = [ $day, esc_url( get_permalink( $post ) ), $title ];

					// the comment count will only appear if comments are open
					// or the post has existing comments
					if ( $show_comments && ( comments_open( $post ) || get_comments_number( $post ) ) ) {

						$template = '%s<dd><a href="%s">%s</a>&nbsp;%s</dd>';
						/* translators: %s: comments count */
						$comments = sprintf( esc_html_x( '(%s)', 'Archives: Clean: Comment Count', 'gpersiandate' ), get_comments_number( $post ) );
						$values[] = sprintf( '<small class="-comments" title="%s">%s</small>', esc_attr( $args['string_count'] ), gPersianDateTranslate::numbers( $comments ) );
					}

					$html.= vsprintf( $template, $values );
				}
			}

			$html.= '</dl><div class="clearfix"></div></li>';
			$html = '<ul class="list-unstyled -archives">'.$html.'</ul>';

			wp_reset_postdata();

		} else if ( ! $args['string_empty'] ) {

			return FALSE;

		} else {

			$html = '<span class="-empty">'.$args['string_empty'].'</span>';
		}

		return sprintf( '<div class="-wrap date-archives-clean %s">%s</div>', $args['css_class'], $html );
	}
}
