<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCalendar extends gPersianDateModuleCore
{

	public static function build( $atts = [], $current_time = NULL )
	{
		global $wpdb;

		$current_date  = gPersianDateDate::getByCal( $current_time, ( isset( $atts['calendar'] ) ? $atts['calendar'] : NULL ) );
		$current_year  = ''.$current_date['year'];
		$current_month = ''.sprintf( '%02d', $current_date['mon'] );
		$current_day   = ''.sprintf( '%02d', $current_date['mday'] );

		$args = self::atts( [
			'calendar' => NULL, // NULL to default

			'this_year'   => $current_year,
			'this_month'  => $current_month,
			'this_day'    => $current_day,
			'week_begins' => get_option( 'start_of_week' ), // '6' // week start on Saturday

			'post_type'        => apply_filters( 'gpersiandate_calendar_posttypes', [ 'post' ] ),
			'exclude_statuses' => NULL, // for admin only // NULL to default

			'initial'      => TRUE,
			'caption'      => TRUE, // year/month table caption / string for custom
			'caption_link' => TRUE, // table caption link to / string for custom
			'navigation'   => TRUE, // next/prev foot nav

			'nav_prev'  => _x( '&laquo; %s', 'Calendar: Build: Previous Month', GPERSIANDATE_TEXTDOMAIN ),
			'nav_next'  => _x( '%s &raquo;', 'Calendar: Build: Next Month', GPERSIANDATE_TEXTDOMAIN ),
			'title_sep' => _x( ', ', 'Calendar: Build: Title Seperator', GPERSIANDATE_TEXTDOMAIN ),

			'link_build_callback' => NULL, // NULL to default
			'the_day_callback'    => NULL, // NULL to default
			'nav_month_callback'  => NULL, // NULL to default

			'id'    => 'wp-calendar', // table html id
			'class' => 'date-calendar', // table html css class
		], $atts );

		// bailing if no posts!
		if ( ! gPersianDateUtilities::hasPosts( $args['post_type'], $args['exclude_statuses'] ) )
			return '';

		if ( ! $args['link_build_callback'] || ! is_callable( $args['link_build_callback'] ) )
			$args['link_build_callback'] = [ 'gPersianDateLinks', 'build' ];

		if ( ! $args['the_day_callback'] || ! is_callable( $args['the_day_callback'] ) )
			$args['the_day_callback'] = [ __CLASS__, 'theDayCallback' ];

		if ( ! $args['nav_month_callback'] || ! is_callable( $args['nav_month_callback'] ) )
			$args['nav_month_callback'] = [ __CLASS__, 'navMonthCallback' ];

		list( $first_day, $last_day ) = gPersianDateDate::monthFirstAndLast( $args['this_year'], $args['this_month'], NULL, $args['calendar'] );

		$post_type_clause = "AND post_type IN ( '".join( "', '", esc_sql( (array) $args['post_type'] ) )."' )";

		$post_status_clause = is_admin()
			? "AND post_status NOT IN ( '".join( "', '", esc_sql(
				gPersianDateUtilities::getExcludeStatuses( $args['exclude_statuses'] ) ) )."' )"
			: "AND post_status = 'publish'";

		$html = $caption = '';

		if ( TRUE === $args['caption'] )
			$caption = self::getCaption( $args['this_year'], $args['this_month'], $args['calendar'] );

		else if ( $args['caption'] )
			$caption = $args['caption'];

		if ( $caption && TRUE === $args['caption_link'] )
			$caption = gPersianDateHTML::link( $caption, call_user_func_array( $args['link_build_callback'], [ 'month', $args['this_year'], $args['this_month'], $args ] ) );

		else if ( $caption && $args['caption_link'] )
			$caption = gPersianDateHTML::link( $caption, $args['caption_link'] );

		if ( $caption )
			$html .= '<caption>'.$caption.'</caption>';

		$html .= '<thead><tr>';

		$myweek = gPersianDateStrings::dayoftheweek( NULL, TRUE, $args['calendar'], FALSE );
		$mydays = gPersianDateStrings::dayoftheweek( NULL, TRUE, $args['calendar'], TRUE );

		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$wd = ( $wdcount + $args['week_begins'] ) % 7;
			$html .= $args['initial'] ? '<th title="'.esc_attr( $myweek[$wd] ).'" data-weekday="'.$wd.'">'.$mydays[$wd].'</th>' : '<th data-weekday="'.$wd.'">'.$myweek[$wd].'</th>';
		}

		$html .= '</tr></thead>';

		if ( $args['navigation'] ) {

			// get the next and previous months
			// with at least one post

			$previous = $wpdb->get_row( "
				SELECT post_date
				FROM {$wpdb->posts}
				WHERE post_date < '{$first_day}'
				{$post_type_clause}
				{$post_status_clause}
				ORDER BY post_date DESC
				LIMIT 1
			" );

			$next = $wpdb->get_row( "
				SELECT post_date
				FROM {$wpdb->posts}
				WHERE post_date > '{$last_day}'
				{$post_type_clause}
				{$post_status_clause}
				ORDER BY post_date ASC
				LIMIT 1
			" );

			$html .= '<tfoot><tr>';

			if ( $previous ) {

				$previous_date = gPersianDateDate::getByCal( $previous->post_date, $args['calendar'] );

				$html .= '<td colspan="3" class="-next-prev -prev" data-month="'.$previous_date['mon'].'" data-year="'.$previous_date['year'].'">';
				$html .= call_user_func_array( $args['nav_month_callback'],
					array( $previous_date, FALSE, $args ) );
				$html .'</td>';

			} else {
				$html .= self::getPad( 3 );
			}

			$html .= '<td class="-middle -pad">&nbsp;</td>';

			if ( $next ) {

				$next_date = gPersianDateDate::getByCal( $next->post_date, $args['calendar'] );

				$html .= '<td colspan="3" class="-next-prev -next" data-month="'.$next_date['mon'].'" data-year="'.$next_date['year'].'">';
					$html .= call_user_func_array( $args['nav_month_callback'],
						array( $next_date, TRUE, $args ) );
				$html .'</td>';

			} else {
				$html .= self::getPad( 3 );
			}

			$html .= '</tr></tfoot>';
		}

		$html .= '<tbody><tr>';

		$data = [];

		$post_select_fields = is_admin()
			? "post_title, post_date, post_type, post_modified, post_status, post_author"
			: "post_title, post_date, post_type";

		$posts = $wpdb->get_results( "
			SELECT ID, {$post_select_fields}, MONTH(post_date) AS month, DAYOFMONTH(post_date) as dom
			FROM {$wpdb->posts}
			WHERE post_date >= '{$first_day}'
			AND post_date <= '{$last_day}'
			{$post_type_clause}
			{$post_status_clause}
		" );

		if ( $posts ) {

			foreach ( (array) $posts as $post ) {

				$key = $post->month.'_'.$post->dom;

				if ( ! isset( $data[$key] ) ) {
					$post_date  = gPersianDateDate::getByCal( $post->post_date, $args['calendar'] );
					$data[$key] = [ 'posts' => [], 'mday' => $post_date['mday'] ];
				}

				$the_post = [
					'ID'    => $post->ID,
					'date'  => $post->post_date,
					'type'  => $post->post_type,
					'title' => $post->post_title,
				];

				if ( is_admin() ) {
					$the_post['modified'] = $post->post_modified;
					$the_post['status']   = $post->post_status;
					$the_post['author']   = $post->post_author;
				}

				$data[$key]['posts'][] = $the_post;
			}

			if ( ! empty( $data ) ) {
				$the_days = wp_list_pluck( $data, 'mday' );
				$data     = array_combine( $the_days, $data );
			}
		}

		if ( $pad = self::mod( date( 'w', strtotime( $first_day ) ) - $args['week_begins'] ) )
			$html .= self::getPad( $pad );

		$days_in_month = gPersianDateDate::daysInMonth( $args['this_month'], $args['this_year'], $args['calendar'] );

		for ( $the_day = 1; $the_day <= $days_in_month; ++$the_day ) {

			if ( isset( $new_row ) && $new_row )
				$html .= '</tr><tr>';

			$new_row = FALSE;

			$today = ( $the_day == $current_day
				&& $args['this_month'] == $current_month
				&& $args['this_year'] == $current_year );

			$the_day_data = array_key_exists( $the_day, $data ) ? $data[$the_day]['posts'] : [];

			$html .= '<td class="-day'.( $today ? ' -today' : '' ).( empty( $the_day_data ) ? '' : ' -with-posts' ).'" data-day="'.$the_day.'">';
				$html .= call_user_func_array( $args['the_day_callback'],
					array( $the_day, $the_day_data, $args, $today ) );
			$html .= '</td>';

			$week_day = gPersianDateDate::dayOfWeek( $args['this_month'], $the_day, $args['this_year'], $args['calendar'] );

			if ( 6 == self::mod( $week_day - $args['week_begins'] ) )
				$new_row = TRUE;
		}

		if ( $pad = ( 6 - self::mod( $week_day - $args['week_begins'] ) ) )
			$html .= self::getPad( $pad );

		return gPersianDateHTML::tag( 'table', [
			'id'    => $args['id'],
			'class' => $args['class'],
			'data'  => [
				'calendar' => $args['calendar'] ?: FALSE,
				'year'     => $args['this_year'],
				'month'    => $args['this_month'],
			],
		], $html.'</tr></tbody>' );
	}

	public static function theDayCallback( $the_day, $data = [], $args = [], $today = FALSE )
	{
		if ( ! count( $data ) )
			return gPersianDateTranslate::numbers( $the_day );

		$titles = [];

		foreach ( $data as $post )
			$titles[] = apply_filters( 'the_title', $post['title'], $post['ID'] );

		return gPersianDateHTML::tag( 'a', [
			'href'  => call_user_func_array( $args['link_build_callback'], [ 'day', $args['this_year'], $args['this_month'], $the_day, $args ] ),
			'title' => implode( $args['title_sep'], $titles ),
		], gPersianDateTranslate::numbers( $the_day ) );
	}

	public static function navMonthCallback( $date, $next = TRUE, $args = [] )
	{
		return gPersianDateHTML::tag( 'a', [
			'href'  => call_user_func_array( $args['link_build_callback'], [ 'month', $date['year'], $date['mon'], NULL, $args ] ),
			'title' => self::getCaption( $date['year'], $date['mon'], $args['calendar'] ),
		], sprintf( ( $next ? $args['nav_next'] : $args['nav_prev'] ), $date['month'] ) );
	}

	public static function getPad( $pad )
	{
		return '<td class="-pad" colspan="'.esc_attr( $pad ).'">&nbsp;</td>';
	}

	public static function getCaption( $year, $month, $calendar = NULL )
	{
		return sprintf(
			_x( '%1$s %2$s', 'Calendar: Build: Caption', GPERSIANDATE_TEXTDOMAIN ),
			gPersianDateStrings::month( $month, FALSE, $calendar ),
			gPersianDateTranslate::numbers( $year )
		);
	}

	// get number of days since the start of the week
	// @SOURCE: `calendar_week_mod()`
	public static function mod( $num, $base = 7 )
	{
		return intval( $num - $base * floor( $num / $base ) );
	}

	// REPLICA: `get_calendar()`
	public static function get( $initial = TRUE, $echo = TRUE )
	{
		global $wpdb, $m, $monthnum, $year, $posts;

		$args = [ 'initial' => $initial ];

		if ( ! empty( $monthnum ) && ! empty( $year ) ) {

			$args['this_year']  = ''.intval( $year );
			$args['this_month'] = ''.zeroise( intval( $monthnum ), 2 );

		} else if ( ! empty( $m ) ) {

			$args['this_year'] = ''.intval( substr( $m, 0, 4 ) );

			if ( strlen( $m ) < 6 )
				$args['this_month'] = '01';

			else
				$args['this_month'] = ''.zeroise( intval( substr( $m, 4, 2 ) ), 2 );
		}

		$key = md5( 'gpersiandate_calendar_'.serialize( $args ) );

		if ( self::isFlush() )
			delete_transient( $key );

		if ( FALSE === ( $html = get_transient( $key ) ) ) {
			$html = self::build( $args );
			$html = gPersianDateUtilities::minifyHTML( $html );
			set_transient( $key, $html, 12 * HOUR_IN_SECONDS );
		}

		if ( ! $echo )
			return $html;

		echo $html;
	}
}
