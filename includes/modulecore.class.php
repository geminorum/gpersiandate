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

	protected static function sanitize_hook( $hook )
	{
		return trim( str_ireplace( [ '-', '.', '/' ], '_', $hook ) );
	}

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

	public static function blockWrap( $html, $suffix = FALSE, $args = [], $block = TRUE, $extra = [] )
	{
		if ( is_null( $html ) )
			return $html;

		$before = empty( $args['before'] ) ? '' : $args['before'];
		$after  = empty( $args['after'] )  ? '' : $args['after'];

		// if ( empty( $args['wrap'] ) )
		// 	return $before.$html.$after;

		$classes = [ '-wrap', 'gpersiandate-wrap-block' ];

		if ( $suffix )
			$classes[] = 'wp-block-gpersiandate-'.$suffix;

		if ( isset( $args['context'] ) && $args['context'] )
			$classes[] = 'context-'.$args['context'];

		if ( ! empty( $args['className'] ) )
			$classes[] = $args['className'];

		if ( $after )
			return $before.gPersianDateHTML::tag( $block ? 'div' : 'span', array_merge( [ 'class' => $classes ], $extra ), $html ).$after;

		return gPersianDateHTML::tag( $block ? 'div' : 'span', array_merge( [ 'class' => $classes ], $extra ), $before.$html );
	}

	protected function register_blocktype( $name, $extra = [], $deps = NULL )
	{
		// checks for WP 5.0
		if ( ! function_exists( 'register_block_type' ) )
			return FALSE;

		$args = [ 'editor_script' => gPersianDateUtilities::registerBlock( $name, $deps ) ];

		if ( ! defined( 'GPERSIANDATE_DISABLE_STYLES' ) || ! GPERSIANDATE_DISABLE_STYLES )
			$args['style'] = gPersianDateUtilities::registerBlockStyle( $name );

		if ( method_exists( $this, 'block_'.$name.'_render_callback' ) )
			$args['render_callback'] = [ $this, 'block_'.$name.'_render_callback' ];

		$block = register_block_type( 'gpersiandate/'.$name, array_merge( $args, $extra ) );

		wp_set_script_translations( $args['editor_script'], 'gpersiandate', GPERSIANDATE_DIR.'languages' );

		return $block;
	}
}
