<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateWordPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'date_i18n', array( $this, 'date_i18n' ), 10, 4 );

		add_filter( 'get_the_date', array( $this, 'get_the_date' ), 10, 3 );
		add_filter( 'get_the_time', array( $this, 'get_the_time' ), 10, 3 );

		add_filter( 'get_comment_date', array( $this, 'get_comment_date' ), 10, 3 );
		// NOTE: get_comment_time has a translate option, but we override b/c time_format
		add_filter( 'get_comment_time', array( $this, 'get_comment_time' ), 10, 5 );

		add_filter( 'wp_title', array( 'gPersianDateTranslate', 'legacy' ), 12 );

		add_filter( 'the_title', array( 'gPersianDateTranslate', 'legacy' ), 12 );
		add_filter( 'the_content', array( $this, 'the_content' ), 12 );
		add_filter( 'get_the_excerpt', array( 'gPersianDateTranslate', 'html' ), 12 );
		add_filter( 'get_comment_excerpt', array( 'gPersianDateTranslate', 'html' ), 12 );
		add_filter( 'get_comment_text', array( 'gPersianDateTranslate', 'html' ), 12 );
		add_filter( 'comments_number', array( 'gPersianDateTranslate', 'numbers' ), 12 );
		add_filter( 'human_time_diff', array( 'gPersianDateTranslate', 'numbers' ), 12 );

		add_filter( 'pre_insert_term', array( $this, 'pre_insert_term' ), 10, 2 );
		add_filter( 'pre_term_name', array( 'gPersianDateTranslate', 'numbers' ) );
		add_filter( 'pre_term_description', array( 'gPersianDateTranslate', 'html' ) );

		add_filter( 'gmeta_meta', array( 'gPersianDateTranslate', 'numbers' ), 12 );
		add_filter( 'gmeta_lead', array( 'gPersianDateTranslate', 'html' ), 12 );
		add_filter( 'geditorial_kses', array( 'gPersianDateTranslate', 'html' ), 12 );

		add_filter( 'list_pages', array( 'gPersianDateTranslate', 'numbers' ), 12 ); // page dropdown walker item title
		add_filter( 'wp_nav_menu_items', array( $this, 'wp_nav_menu_items' ), 10, 2 );

		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 );
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
		$time   = gPersianDateDate::postDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'date' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_the_time( $the_time, $d, $post )
	{
		$time   = gPersianDateDate::postDate( $post );
		$format = gPersianDateFormat::sanitize( $d, 'time' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_comment_date( $date, $d, $comment )
	{
		$time   = gPersianDateDate::commentDate( $comment );
		$format = gPersianDateFormat::sanitize( $d, 'date' );

		return gPersianDateDate::to( $format, $time );
	}

	public function get_comment_time( $date, $d, $gmt, $translate, $comment )
	{
		if ( $translate ) {
			$time   = gPersianDateDate::commentDate( $comment, $gmt );
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

		// TODO: check if needs!
		$items = preg_replace( '%{TODAY_DATE}%', gPersianDateDate::to( $format ), $items );
		$items = preg_replace( '%{TODAY_DATE_HIJRI}%', gPersianDateDate::toHijri( $format ), $items );

		return $items;
	}
}
