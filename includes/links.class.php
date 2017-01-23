<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateLinks extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'posts_where', array( $this, 'posts_where' ), 20 );

		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );
		add_filter( 'day_link', array( $this, 'day_link' ), 10, 4 );
		add_filter( 'month_link', array( $this, 'month_link' ), 10, 3 );
		add_filter( 'year_link', array( $this, 'year_link' ), 10, 2 );

		add_filter( 'wp_title_parts', array( $this, 'wp_title_parts' ) );
	}

	public function posts_where( $where = '' )
	{
		global $wpdb, $wp_query;

		if ( is_admin() || ! $wp_query->is_main_query() )
			return $where;

		$conversion = FALSE;

		$start = $end = array(
			'year'   => 1,
			'month'  => 1,
			'day'    => 1,
			'hour'   => 0,
			'minute' => 0,
			'second' => 0,
		);

		if ( ! empty( $wp_query->query_vars['m'] ) ) {

			$m = ''.preg_replace( '|[^0-9]|', '', $wp_query->query_vars['m'] );
			$start['year'] = substr( $m, 0, 4 );

			if ( $start['year'] < 1700 ) {

				$conversion = TRUE;

				$end['year'] = $start['year'] + 1;

				if ( strlen( $m ) > 5 ) {
					$start['month'] = substr( $m, 4, 2 );
					$end['year']    = $start['year'];
					$end['month']   = $start['month'] + 1;
				}

				if ( strlen( $m ) > 7 ) {
					$start['day'] = substr( $m, 6, 2 );
					$end['month'] = $start['month'];
					$end['day']   = $start['day'] + 1;
				}

				if ( strlen( $m ) > 9 ) {
					$start['hour'] = substr( $m, 8, 2 );
					$end['day']    = $start['day'];
					$end['hour']   = $start['hour'] + 1;
				}

				if ( strlen( $m ) > 11 ) {
					$start['minute'] = substr( $m, 10, 2 );
					$end['hour']     = $start['hour'];
					$end['minute']   = $start['minute'] + 1;
				}

				if ( strlen( $m ) > 13 ) {
					$start['second'] = substr( $m, 12, 2 );
					$end['minute']   = $start['minute'];
					$end['second']   = $start['second'] + 1;
				}
			}

		} else if ( ! empty( $wp_query->query_vars['year'] )
			&& ( (int) $wp_query->query_vars['year'] < 1700 ) ) {

			$conversion = TRUE;

			$start['year'] = $wp_query->query_vars['year'];
			$end['year']   = $start['year'] + 1;

			if ( ! empty( $wp_query->query_vars['monthnum'] ) ) {
				$start['month'] = $wp_query->query_vars['monthnum'];
				$end['year']    = $start['year'];
				$end['month']   = $start['month'] + 1;
			}

			if ( ! empty( $wp_query->query_vars['day'] ) ) {
				$start['day'] = $wp_query->query_vars['day'];
				$end['month'] = $start['month'];
				$end['day']   = $start['day'] + 1;
			}

			if ( ! empty( $wp_query->query_vars['hour'] ) ) {
				$start['hour'] = $wp_query->query_vars['hour'];
				$end['day']    = $start['day'];
				$end['hour']   = $start['hour'] + 1;
			}

			if ( ! empty( $wp_query->query_vars['minute'] ) ) {
				$start['minute'] = $wp_query->query_vars['minute'];
				$end['hour']     = $start['hour'];
				$end['minute']   = $start['minute'] + 1;
			}

			if ( ! empty( $wp_query->query_vars['second'] ) ) {
				$start['second'] = $wp_query->query_vars['second'];
				$end['minute']   = $start['minute'];
				$end['second']   = $start['second'] + 1;
			}
		}

		if ( ! $conversion )
			return $where;

		$days  = gPersianDateDate::daysInMonth();
		$where = self::stripDateClauses( $where );

		if ( $end['second'] > 59 ) {
			$end['second'] = 0;
			$end['minute']++;
		}

		if ( $end['minute'] > 59 ) {
			$end['minute'] = 0;
			$end['hour']++;
		}

		if ( $end['hour'] > 23 ) {
			$end['hour'] = 0;
			$end['day']++;
		}

		if ( $end['day'] > $days[$start['month']-1] ){
			$end['day'] = 1;
			$end['month']++;
		}

		if ( $end['month'] > 12 ) {
			$end['month'] = 1;
			$end['year']++;
		}

		$start_date = gPersianDateDate::makeMySQLFromArray( $start );
		$end_date   = gPersianDateDate::makeMySQLFromArray( $end );

		return $where." AND $wpdb->posts.post_date >= '$start_date' AND $wpdb->posts.post_date < '$end_date' ";
	}

	public static function stripDateClauses( $where )
	{
		$patterns = array(
			'/YEAR\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
			'/DAYOFMONTH\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
			'/MONTH\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
			'/HOUR\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
			'/MINUTE\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
			'/SECOND\((.*?)post_date\s*\)\s*=\s*[0-9\']*/',
		);

		foreach ( $patterns as $pattern )
			$where = preg_replace( $pattern, '1=1', $where );

		return $where;
	}

	public static function build( $for, $year = NULL, $month = NULL, $day = NULL )
	{
		global $wp_rewrite;

		$link = '';

		switch ( $for ) {

			case 'day':

				if ( $link = $wp_rewrite->get_day_permastruct() ) {

					$link = user_trailingslashit( str_replace(
						array( '%year%', '%monthnum%', '%day%' ),
						array( $year, $month, $day ),
						$link
					), 'day' );

				} else {
					$link = '?m='.$year.$month.$day;
				}

			break;
			case 'month':

				if ( $link = $wp_rewrite->get_month_permastruct() ) {

					$link = user_trailingslashit( str_replace(
						array( '%year%', '%monthnum%' ),
						array( $year, $month ),
						$link
					), 'month' );

				} else {
					$link = '?m='.$year.$month;
				}

			break;
			case 'year':

				if ( $link = $wp_rewrite->get_year_permastruct() ) {

					$link = user_trailingslashit( str_replace(
						'%year%',
						$year,
						$link
					), 'year' );

				} else {
					$link = '?m='.$year;
				}
		}

		return home_url( $link );
	}

	public function day_link( $daylink, $year, $month, $day )
	{
		if ( $year != gmdate( 'Y', current_time( 'timestamp' ) ) )
			return $daylink;

		if ( $month != gmdate( 'm', current_time( 'timestamp' ) ) )
			return $daylink;

		if ( $day != gmdate( 'j', current_time( 'timestamp' ) ) )
			return $daylink;

		$date = $year.'-'.$month.'-'.$day;

		return self::build( 'day',
			gPersianDateDate::_to( 'Y', $date ),
			gPersianDateDate::_to( 'm', $date ),
			gPersianDateDate::_to( 'd', $date )
		);
	}

	public function month_link( $monthlink, $year, $month )
	{
		if ( $year != gmdate( 'Y', current_time( 'timestamp' ) ) )
			return $monthlink;

		if ( $month != gmdate( 'm', current_time( 'timestamp' ) ) )
			return $monthlink;

		$date = $year.'-'.$month.'-01';

		return self::build( 'month',
			gPersianDateDate::_to( 'Y', $date ),
			gPersianDateDate::_to( 'm', $date )
		);
	}

	public function year_link( $yearlink, $year )
	{
		if ( $year != gmdate( 'Y', current_time( 'timestamp' ) ) )
			return $yearlink;

		$date = $year.'-01-01';

		return self::build( 'year',
			gPersianDateDate::_to( 'Y', $date )
		);
	}

	public function post_link( $permalink, $post, $leavename )
	{
		if ( FALSE !== strpos( $permalink, '?p=' ) )
			return $permalink;

		if ( in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft', 'future' ) ) )
			return $permalink;

		if ( ! $structure = apply_filters( 'pre_post_link', get_option( 'permalink_structure' ), $post, $leavename ) )
			return $permalink;

		if ( FALSE === strpos( $structure, '%year%' )
			&& FALSE === strpos( $structure, '%monthnum%' )
			&& FALSE === strpos( $structure, '%day%' ) )
				return $permalink;

		$category = $author = '';

		if ( FALSE !== strpos( $structure, '%category%' ) ) {

			if ( $cats = get_the_category( $post->ID ) ) {

				// FIXME: will dep on WP v4.7.0
				// $cats = wp_list_sort( $cats, array( 'term_id' => 'ASC' ) );
				usort( $cats, '_usort_terms_by_ID' );

				$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );
				$category_object = get_term( $category_object, 'category' );
				$category = $category_object->slug;

				if ( $parent = $category_object->parent )
					$category = get_category_parents( $parent, FALSE, '/', TRUE ).$category;
			}

			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty( $category ) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				if ( $default_category && ! is_wp_error( $default_category ) )
					$category = $default_category->slug;
			}
		}

		if ( FALSE !== strpos( $structure, '%author%' ) )
			$author = get_userdata( $post->post_author )->user_nicename;

		$date = explode( '-', gPersianDateDate::_to( 'Y-m-d-H-i-s', $post->post_date ) );

		$rewritereplace = array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);

		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename ? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename ? '' : '%pagename%',
		);

		return home_url( user_trailingslashit( str_replace(
			$rewritecode,
			$rewritereplace,
			$structure
		), 'single' ) );
	}

	public function wp_title_parts( $title_array )
	{
		if ( is_archive() ) {

			$m     = get_query_var( 'm' );
			$year  = get_query_var( 'year' );
			$title = '';
			$t_sep = '%WP_TITILE_SEP%'; // temporary separator

			if ( ! empty( $m ) ) {
				$my_year  = substr( $m, 0, 4 );
				$my_month = gPersianDateStrings::month( substr( $m, 4, 2 ) );
				$my_day   = intval( substr( $m, 6, 2 ) );
				$title    = $my_year.( $my_month ? $t_sep.$my_month : '' ).( $my_day ? $t_sep.$my_day : '' );
			}

			if ( ! empty( $year ) ) {
				$title    = $year;
				$monthnum = get_query_var( 'monthnum' );
				$day      = get_query_var( 'day' );

				if ( ! empty( $monthnum ) )
					$title .= $t_sep.gPersianDateStrings::month( $monthnum );

				if ( ! empty( $day ) )
					$title .= $t_sep.zeroise( $day, 2 );
			}

			if ( $title )
				return explode( $t_sep, gPersianDateTranslate::numbers( $title ) );
		}

		return $title_array;
	}
}
