<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateShortCodes extends gPersianDateModuleCore
{

	protected function setup_actions()
	{
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init()
	{
		$this->shortcodes( array(
			'date-archives-clean'   => 'shortcode_archives_clean',
			'date-archives-compact' => 'shortcode_archives_compact',
		) );
	}

	public function shortcode_archives_clean( $atts = array(), $content = NULL, $tag = '' )
	{
		$args = shortcode_atts( array(
			'context' => NULL,
			'wrap'    => TRUE,
			'before'  => '',
			'after'   => '',
		), $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$html = gPersianDateArchives::getClean( $atts );

		return self::shortcodeWrap( $html, 'archives-clean', $args );
	}

	public function shortcode_archives_compact( $atts = array(), $content = NULL, $tag = '' )
	{
		$args = shortcode_atts( array(
			'context' => NULL,
			'wrap'    => TRUE,
			'before'  => '',
			'after'   => '',
		), $atts, $tag );

		if ( FALSE === $args['context'] )
			return NULL;

		$html = gPersianDateArchives::getCompact( $atts );

		return self::shortcodeWrap( $html, 'archives-compact', $args );
	}
}
