<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateModuleCore extends gPersianDateBase
{

	protected $ajax = FALSE;

	public function __construct()
	{
		if ( ! $this->ajax && self::isAJAX() )
			throw new \Exception( 'Not on AJAX Calls!' );

		if ( wp_installing() )
			throw new \Exception( 'Not while WP is Installing!' );

		$this->setup_actions();
	}

	protected function setup_actions() {}

	protected function shortcodes( $shortcodes = [] )
	{
		foreach ( $shortcodes as $shortcode => $method ) {
			remove_shortcode( $shortcode );
			add_shortcode( $shortcode, [ $this, $method ] );
		}
	}

	public static function shortcodeWrap( $html, $suffix = FALSE, $args = [], $block = TRUE )
	{
		$before = empty( $args['before'] ) ? '' : $args['before'];
		$after  = empty( $args['after'] )  ? '' : $args['after'];

		if ( empty( $args['wrap'] ) )
			return $before.$html.$after;

		$classes = [ '-wrap', 'gpersiandate-wrap-shortcode' ];

		if ( $suffix )
			$classes[] = 'shortcode-'.$suffix;

		if ( isset( $args['context'] ) && $args['context'] )
			$classes[] = 'context-'.$args['context'];

		if ( $after )
			return $before.gPersianDateHTML::tag( $block ? 'div' : 'span', [ 'class' => $classes ], $html ).$after;

		return gPersianDateHTML::tag( $block ? 'div' : 'span', [ 'class' => $classes ], $before.$html );
	}
}
