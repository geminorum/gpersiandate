<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateModuleCore extends gPersianDateBase
{

	protected $ajax = FALSE;

	public function __construct()
	{
		if ( ! $this->ajax && self::isAJAX() )
			throw new \Exception( 'Not on AJAX Calls!' );

		// wp-activate works only for network enabled plugins!
		// @SEE: https://core.trac.wordpress.org/ticket/23197
		// if ( wp_installing() && 'wp-activate.php' !== gPersianDateWP::pageNow() )
		// 	throw new \Exception( 'Not while WP is Installing!' );

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

	public static function shortcodeWrap( $html, $suffix = FALSE, $args = [], $block = TRUE, $extra = [] )
	{
		if ( is_null( $html ) )
			return $html;

		$before = empty( $args['before'] ) ? '' : $args['before'];
		$after  = empty( $args['after'] )  ? '' : $args['after'];

		if ( empty( $args['wrap'] ) )
			return $before.$html.$after;

		$classes = [ '-wrap', 'gpersiandate-wrap-shortcode' ];

		if ( $suffix )
			$classes[] = 'shortcode-'.$suffix;

		if ( isset( $args['context'] ) && $args['context'] )
			$classes[] = 'context-'.$args['context'];

		if ( ! empty( $args['class'] ) )
			$classes[] = $args['class'];

		if ( $after )
			return $before.gPersianDateHTML::tag( $block ? 'div' : 'span', array_merge( [ 'class' => $classes ], $extra ), $html ).$after;

		return gPersianDateHTML::tag( $block ? 'div' : 'span', array_merge( [ 'class' => $classes ], $extra ), $before.$html );
	}
}
