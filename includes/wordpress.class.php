<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateWordPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'date_i18n', [ $this, 'date_i18n' ], 10, 4 );

		add_filter( 'get_the_date', [ $this, 'get_the_date' ], 10, 3 );
		add_filter( 'get_the_time', [ $this, 'get_the_time' ], 10, 3 );
		add_filter( 'get_the_modified_date', [ $this, 'get_the_modified_date' ], 10, 3 );
		add_filter( 'get_the_modified_time', [ $this, 'get_the_modified_time' ], 10, 3 );

		add_filter( 'get_comment_date', [ $this, 'get_comment_date' ], 10, 3 );
		// NOTE: get_comment_time has a translate option, but we override b/c time_format
		add_filter( 'get_comment_time', [ $this, 'get_comment_time' ], 10, 5 );

		add_filter( 'wp_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );
		add_filter( 'document_title_parts', [ 'gPersianDateTranslate', 'array_map_legacy' ], 12 );

		add_filter( 'the_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );
		add_filter( 'the_content', [ $this, 'the_content' ], 12 );
		add_filter( 'get_the_excerpt', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'get_comment_excerpt', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'get_comment_text', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'comments_number', [ 'gPersianDateTranslate', 'numbers' ], 12 );
		add_filter( 'human_time_diff', [ 'gPersianDateTranslate', 'numbers' ], 12 );

		add_filter( 'single_post_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );
		add_filter( 'single_cat_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );
		add_filter( 'single_tag_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );
		// add_filter( 'single_month_title', [ 'gPersianDateTranslate', 'legacy' ], 12 ); // no need
		add_filter( 'nav_menu_attr_title', [ 'gPersianDateTranslate', 'legacy' ], 12 );

		add_filter( 'nav_menu_description', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'wp_get_attachment_caption', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'wp_link_pages_link', [ 'gPersianDateTranslate', 'html' ], 12 );

		// add_filter( 'pre_insert_term', [ $this, 'pre_insert_term' ], 10, 2 );
		add_filter( 'pre_term_name', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'pre_term_description', [ 'gPersianDateTranslate', 'html' ] );

		add_filter( 'gmeta_meta', [ 'gPersianDateTranslate', 'numbers' ], 12 );
		add_filter( 'gmeta_lead', [ 'gPersianDateTranslate', 'html' ], 12 );
		add_filter( 'geditorial_kses', [ 'gPersianDateTranslate', 'html' ], 12 );

		add_filter( 'list_pages', [ 'gPersianDateTranslate', 'numbers' ], 12 ); // page dropdown walker item title
		add_filter( 'wp_nav_menu_items', [ $this, 'wp_nav_menu_items' ], 10, 2 );

		add_action( 'widgets_init', [ $this, 'widgets_init' ], 20 );
	}

	public function widgets_init()
	{
		global $wp_widget_factory;

		$wp_widget_factory->widgets['WP_Widget_Archives'] = new WP_Widget_Persian_Archives();
		$wp_widget_factory->widgets['WP_Widget_Calendar'] = new WP_Widget_Persian_Calendar();
	}

	public function date_i18n( $j, $req_format, $i, $gmt )
	{
		if ( FALSE === $i )
			$i = current_time( 'mysql', $gmt );
		else
			$i = date( 'Y-m-d H:i:s', $i );

		$format = gPersianDateFormat::sanitize( $req_format, 'i18n' );

		return gPersianDateDate::to( $format, $i );
	}

	public function get_the_date( $the_date, $d, $post )
	{
		$time   = self::postDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'date' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_the_time( $the_time, $d, $post )
	{
		$time   = self::postDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'time' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_the_modified_date( $the_time, $d, $post = NULL )
	{
		$time   = self::postModifiedDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'date' );

		return FALSE === $time ? $the_time : gPersianDateDate::to( $format, $time );
	}

	public function get_the_modified_time( $the_time, $d, $post = NULL )
	{
		$time   = self::postModifiedDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'time' );

		return gPersianDateDate::to( $format, $time );
		return FALSE === $time ? $the_time : gPersianDateDate::to( $format, $time );
	}

	public function get_comment_date( $date, $d, $comment )
	{
		$time   = self::commentDate( $comment );
		$format = gPersianDateFormat::sanitize( $d, 'date' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_comment_time( $date, $d, $gmt, $translate, $comment )
	{
		if ( $translate ) {
			$time   = self::commentDate( $comment, $gmt );
			$format = gPersianDateFormat::sanitize( $d, 'time' );

			return gPersianDateDate::to( $format, $time );
		}

		return $date;
	}

	public function the_content( $content )
	{
		if ( defined( 'GPERSIANDATE_SKIP' ) && GPERSIANDATE_SKIP )
			return $content;

		if ( $content )
			return gPersianDateTranslate::html( $content );

		return $content;
	}

	public function pre_insert_term( $term, $taxonomy )
	{
		if ( ! is_int( $term ) )
			return gPersianDateTranslate::numbers( $term );

		return $term;
	}

	// Menu Navigation Date handler
	// just put {TODAY_DATE}/{TODAY_DATE_HIJRI} on a menu item text!
	// TODO: disable option, format option, full date for title attr
	public function wp_nav_menu_items( $items, $args )
	{
		$format = 'j F Y'; // 'j M Y'

		if ( gPersianDateText::has( $items, '{TODAY_DATE}' ) ) {

			if ( ! isset( $this->today_date ) )
				$this->today_date = gPersianDateDate::to( $format );

			$items = preg_replace( '%{TODAY_DATE}%', $this->today_date, $items );
		}

		if ( gPersianDateText::has( $items, '{TODAY_DATE_HIJRI}' ) ) {

			if ( ! isset( $this->today_date ) )
				$this->today_date = gPersianDateDate::toHijri( $format );

			$items = preg_replace( '%{TODAY_DATE_HIJRI}%', $this->today_date, $items );
		}

		return $items;
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

	public static function getPosttypeFirstAndLast( $post_types = 'post', $args = [], $user_id = 0, $protected = TRUE )
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

		return [
			( count( $first ) ? $first[0]->date : '' ),
			( count( $last )  ? $last[0]->date  : '' ),
		];
	}

	public static function getPostTypeMonths( $post_type = 'post', $args = [], $user_id = 0 )
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

		$list = [];
		$last = FALSE;

		foreach ( $months as $row ) {

			if ( 0 == $row->year )
				continue;

			$date  = mktime( 0 ,0 , 0, zeroise( $row->month, 2 ), $row->day, $row->year );
			$month = gPersianDateDate::_to( 'Ym', $date );

			if ( $last != $month )
				$list[$month] = gPersianDateDate::to( 'M Y', $date );

			$last = $month;
		}

		return $list;
	}
}
