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
		], $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$key = md5( 'gpersiandate_clean_'.serialize( $atts ).'_'.$tag );

		if ( self::isFlush() )
			delete_transient( $key );

		if ( FALSE === ( $html = get_transient( $key ) ) ) {
			$html = gPersianDateArchives::getClean( $atts );
			$html = gPersianDateUtilities::minifyHTML( $html );
			set_transient( $key, $html, 12 * HOUR_IN_SECONDS );
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
		], $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$key = md5( 'gpersiandate_compact_'.serialize( $atts ).'_'.$tag );

		if ( self::isFlush() )
			delete_transient( $key );

		if ( FALSE === ( $html = get_transient( $key ) ) ) {
			$html = gPersianDateArchives::getCompact( $atts );
			$html = gPersianDateUtilities::minifyHTML( $html );
			set_transient( $key, $html, 12 * HOUR_IN_SECONDS );
		}

		return self::shortcodeWrap( $html, 'archives-compact', $args );
	}
}
