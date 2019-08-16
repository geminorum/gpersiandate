<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateShortCodes extends gPersianDateModuleCore
{

	protected function setup_actions()
	{
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init()
	{
		$this->shortcodes( [
			'date-archives-clean'   => 'shortcode_archives_clean',
			'date-archives-compact' => 'shortcode_archives_compact',
			'entry-link-published'  => 'shortcode_entry_published',
		] );
	}

	/***
		[clean-my-archives year="1396"]
		<!-- nextpage -->
		[clean-my-archives year="1395"]
		<!-- nextpage -->
		[clean-my-archives year="1394"]
	**/
	public function shortcode_archives_clean( $atts = [], $content = NULL, $tag = '' )
	{
		$args = shortcode_atts( [
			'context' => NULL,
			'wrap'    => TRUE,
			'before'  => '',
			'after'   => '',
			'ttl'     => 12 * HOUR_IN_SECONDS,
		], $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$key = md5( 'gpersiandate_clean_'.serialize( $args ).'_'.$tag );

		if ( self::isFlush() )
			delete_transient( $key );

		if ( FALSE === ( $html = get_transient( $key ) ) ) {

			if ( empty( $atts ) )
				$atts = [];

			// passing context into the generator
			$atts['row_context'] = $args['context'] ?: FALSE;

			$html = gPersianDateArchives::getClean( $atts );
			// $html = gPersianDateUtilities::minifyHTML( $html ); // causes problem on large data

			set_transient( $key, $html, $args['ttl'] );
		}

		return self::shortcodeWrap( $html, 'archives-clean', $args );
	}

	public function shortcode_archives_compact( $atts = [], $content = NULL, $tag = '' )
	{
		$args = shortcode_atts( [
			'context' => NULL,
			'wrap'    => TRUE,
			'before'  => '',
			'after'   => '',
			'ttl'     => 12 * HOUR_IN_SECONDS,
		], $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$key = md5( 'gpersiandate_compact_'.serialize( $args ).'_'.$tag );

		if ( self::isFlush() )
			delete_transient( $key );

		if ( FALSE === ( $html = get_transient( $key ) ) ) {

			$html = gPersianDateArchives::getCompact( $atts );
			$html = gPersianDateUtilities::minifyHTML( $html );

			set_transient( $key, $html, $args['ttl'] );
		}

		return self::shortcodeWrap( $html, 'archives-compact', $args );
	}

	// @REF: http://justintadlock.com/?p=2507
	public function shortcode_entry_published( $atts = array(), $content = NULL, $tag = '' )
	{
		$args = shortcode_atts( [
			'format'  => get_option( 'date_format' ),
			/* translators: %s: title */
			'title'   => _x( 'Archive for %s', 'Shortcodes: Entry Published Title', 'gpersiandate' ),
			'context' => NULL,
			'wrap'    => TRUE,
			'before'  => '',
			'after'   => '',
		], $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$html = '';

		$year  = get_the_time( 'Y' );
		$month = get_the_time( 'm' );
		$day   = get_the_time( 'j' );

		$number_year  = gPersianDateTranslate::numbers_back( $year );
		$number_month = gPersianDateTranslate::numbers_back( $month );
		$number_day   = gPersianDateTranslate::numbers_back( $day );

		$url_year  = gPersianDateLinks::build( 'year', $number_year );
		$url_month = gPersianDateLinks::build( 'month', $number_year, $number_month );
		$url_day   = gPersianDateLinks::build( 'day', $number_year, $number_month, $number_day );

		$link_year  = '<a href="'.esc_url( $url_year ).'" title="'.esc_attr( sprintf( $args['title'], $year ) ).'" rel="nofollow">'.$year.'</a>';
		$link_month = '<a href="'.esc_url( $url_month ).'" title="'.esc_attr( sprintf( $args['title'], get_the_time( 'F Y' ) ) ).'" rel="nofollow">'.get_the_time( 'F' ).'</a>';
		$link_day   = '<a href="'.esc_url( $url_day ).'" title="'.esc_attr( sprintf( $args['title'], get_the_time( $args['format'] ) ) ).'" rel="nofollow">'.$day.'</a>';

		// FIXME: problem with slashes in the format
		// $format = is_rtl() ? strrev( $args['format'] ) : $args['format'];
		$format = $args['format'];

		for ( $i = 0; $i < strlen( $format ); $i++ ) {

			if ( preg_match( '/[oYy]/', $format[$i] ) )
				$html.= $link_year;

			else if ( preg_match( '/[FmMn]/', $format[$i] ) )
				$html.= $link_month;

			else if ( preg_match( '/[dDjl]/', $format[$i] ) )
				$html.= $link_day;

			else
				$html.= $format[$i];
		}

		// $html = '&lrm;'.$html.'&rlm;';

		return self::shortcodeWrap( $html, 'link-published', $args, FALSE );
	}
}
