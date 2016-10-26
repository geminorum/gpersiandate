<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateLinks extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		if ( is_admin() )
			return;

		add_filter( 'posts_where', array( $this, 'posts_where' ), 20 );

		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );
		add_filter( 'day_link', array( $this, 'day_link' ), 10, 4 );
		add_filter( 'month_link', array( $this, 'month_link' ), 10, 3 );
		add_filter( 'year_link', array( $this, 'year_link' ), 10, 2 );

		add_filter( 'wp_title_parts', array( $this, 'wp_title_parts' ) );
	}

	// Originally from wp-jalali
	public function posts_where( $where = '' )
	{
		global $wpdb, $wp_query;

		if ( is_admin() || ! $wp_query->is_main_query() )
			return $where;

		$conversion    = FALSE;
		$days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$start = $end = array(
			'year'     => 1,
			'monthnum' => 1,
			'day'      => 1,
			'hour'     => 0,
			'minute'   => 0,
			'second'   => 0,
		);

		if ( isset( $wp_query->query_vars['m'] )
			&& ! empty( $wp_query->query_vars['m'] ) ) {

			$m = ''.preg_replace( '|[^0-9]|', '', $wp_query->query_vars['m'] );
			$start['year'] = substr( $m, 0, 4 );

			if ( $start['year'] < 1700 ) {

				$conversion = true;
				$end['year'] = $start['year'] + 1;

				if ( strlen( $m ) > 5 ) {
					$start['monthnum'] = substr( $m, 4, 2 );
					$end['year']       = $start['year'];
					$end['monthnum']   = $start['monthnum'] + 1;
				}

				if ( strlen( $m ) > 7 ) {
					$start['day']    = substr( $m, 6, 2 );
					$end['monthnum'] = $start['monthnum'];
					$end['day']      = $start['day'] + 1;
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

		} else if ( isset( $wp_query->query_vars['year'] )
			&& ! empty( $wp_query->query_vars['year'] )
			&& ( (int) $wp_query->query_vars['year'] < 1700 ) ) {

			$conversion = true;
			$start['year'] = $wp_query->query_vars['year'];
			$end['year'] = $start['year'] + 1;

			if ( isset( $wp_query->query_vars['monthnum'] )
				&& ! empty( $wp_query->query_vars['monthnum'] ) ) {
					$start['monthnum'] = $wp_query->query_vars['monthnum'];
					$end['year']       = $start['year'];
					$end['monthnum']   = $start['monthnum'] + 1;
			}

			if ( isset( $wp_query->query_vars['day'] )
				&& ! empty( $wp_query->query_vars['day'] ) ) {
					$start['day']    = $wp_query->query_vars['day'];
					$end['monthnum'] = $start['monthnum'];
					$end['day']      = $start['day'] + 1;
			}

			if ( isset( $wp_query->query_vars['hour'] )
				&& ! empty( $wp_query->query_vars['hour'] ) ) {
					$start['hour'] = $wp_query->query_vars['hour'];
					$end['day']    = $start['day'];
					$end['hour']   = $start['hour'] + 1;
			}

			if ( isset( $wp_query->query_vars['minute'] )
				&& ! empty( $wp_query->query_vars['minute'] ) ) {
					$start['minute'] = $wp_query->query_vars['minute'];
					$end['hour']     = $start['hour'];
					$end['minute']   = $start['minute'] + 1;
			}

			if ( isset( $wp_query->query_vars['second'] )
				&& ! empty( $wp_query->query_vars['second'] ) ) {
					$start['second'] = $wp_query->query_vars['second'];
					$end['minute']   = $start['minute'];
					$end['second']   = $start['second'] + 1;
			}
		}

		if ( ! $conversion )
			return $where;

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

		if ( $end['day'] > $days_in_month[$start['monthnum']-1] ){
			$end['day'] = 1;
			$end['monthnum']++;
		}

		if ( $end['monthnum'] > 12 ) {
			$end['monthnum'] = 1;
			$end['year']++;
		}

		$start_date = date( 'Y-m-d H:i:s', gPersianDateDate::make( $start['hour'], $start['minute'], $start['second'], $start['monthnum'], $start['day'], $start['year'] ) );
		$end_date   = date( 'Y-m-d H:i:s', gPersianDateDate::make( $end['hour'], $end['minute'], $end['second'], $end['monthnum'], $end['day'], $end['year'] ) );

		$where .= " AND $wpdb->posts.post_date >= '$start_date' AND $wpdb->posts.post_date < '$end_date' ";
		return $where;
	}

	public static function stripDateClauses( $where )
	{
		$patterns = array(
			"YEAR\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{4}'*",
			"DAYOFMONTH\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"MONTH\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"HOUR\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"MINUTE\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"SECOND\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
		);

		foreach ( $patterns as $pattern )
			$where = preg_replace( '/'.$pattern.'/', '1=1', $where );

		return $where;
	}

	public static function build( $for, $jyear = NULL, $jmonth = NULL, $jday = NULL )
	{
		global $wp_rewrite;

		$link = '';

		switch( $for ) {
			case 'day' :
				$link = $wp_rewrite->get_day_permastruct();
				if ( ! empty( $link ) ) {
					$link = str_replace(
						array( '%year%', '%monthnum%', '%day%' ),
						array( $jyear, $jmonth, $jday ),
						$link
					);
					$link = user_trailingslashit( $link, 'day' );
				} else {
					$link = '?m='.$jyear.$jmonth.$jday;
				}


			break;
			case 'month' :
				$link = $wp_rewrite->get_month_permastruct();

				if ( ! empty( $monthlink ) ) {
					$link = str_replace(
						array( '%year%', '%monthnum%' ),
						array( $jyear, $jmonth ),
						$link
					);
					$link = user_trailingslashit( $link, 'month' );
				} else {
					$link = '?m='.$jyear.$jmonth;
				}
			break;

			case 'year' :
				$link = $wp_rewrite->get_year_permastruct();

				if ( ! empty( $yearlink ) ) {
					$link = str_replace( '%year%', $jyear, $link );
					$link = user_trailingslashit( $yearlink, 'year' );
				} else {
					$link = '?m='.$jyear;
				}
			break;
		}

		return home_url( $link );
	}

	public function day_link( $link, $year, $month, $day )
	{
		// check if gregorian date or jalali
		if ( $year == gmdate( 'Y', current_time( 'timestamp' ) ) ) {

			$the_time = $year.'-'.$month.'-'.$day;
			return self::build( 'day',
				gPersianDateDate::to( 'Y', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE ),
				gPersianDateDate::to( 'm', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE ),
				gPersianDateDate::to( 'd', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE )
			);
		}

		return $link;
	}

	// ISSUE : must convert year/month back from filter args!
	public function month_link( $link, $year, $month )
	{
		// check if gregorian date or jalali
		if ( $year == gmdate( 'Y', current_time( 'timestamp' ) ) ) {

			$the_time = $year.'-'.$month.'-15';
			return self::build( 'month',
				gPersianDateDate::to( 'Y', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE ),
				gPersianDateDate::to( 'm', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE )
			);
		}

		return $link;
	}

	public function year_link( $link, $year )
	{
		// check if gregorian date or jalali
		if ( $year == gmdate( 'Y', current_time( 'timestamp' ) ) ) {

			$the_time = $year.'-06-15';
			return self::build( 'year',
				gPersianDateDate::to( 'Y', $the_time, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE )
			);

		}

		return $link;
	}

	public function post_link( $permalink, $post, $leavename )
	{
		if ( FALSE !== strpos( $permalink, '?p=' ) )
			return $permalink;

		if ( in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
			return $permalink;

		$permalink_structure = apply_filters( 'pre_post_link', get_option( 'permalink_structure' ), $post, $leavename );
		if ( ! $permalink_structure )
			return $permalink;

		if ( FALSE === strpos( $permalink_structure, '%year%' )
			&& FALSE === strpos( $permalink_structure, '%monthnum%' )
			&& FALSE === strpos( $permalink_structure, '%day%' ) )
				return $permalink;

		$category = '';
		if ( FALSE !== strpos( $permalink_structure, '%category%' ) ) {
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				usort( $cats, '_usort_terms_by_ID'); // order by ID
				$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );
				$category_object = get_term( $category_object, 'category' );
				$category = $category_object->slug;
				if ( $parent = $category_object->parent )
					$category = get_category_parents( $parent, FALSE, '/', true ).$category;
			}
			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty( $category ) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
			}
		}

		$author = '';
		if ( FALSE !== strpos( $permalink_structure, '%author%' ) ) {
			$authordata = get_userdata( $post->post_author );
			$author = $authordata->user_nicename;
		}

		// $date = explode( " ", self::date( 'Y m d H i s', strtotime( $post->post_date ), 'UTC', GPERSIANDATE_LOCALE, false ) );
		$date = explode( '-', gPersianDateDate::to( 'Y-m-d-H-i-s', $post->post_date, GPERSIANDATE_TIMEZONE, GPERSIANDATE_LOCALE, FALSE ) );

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

		$permalink = home_url( str_replace( $rewritecode, $rewritereplace, $permalink_structure ) );
		return user_trailingslashit( $permalink, 'single' );
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
