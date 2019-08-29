<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateUtilities extends gPersianDateBase
{

	public static function registerBlock( $asset, $dep = NULL, $version = GPERSIANDATE_VERSION, $base = GPERSIANDATE_URL, $path = 'assets/blocks' )
	{
		$dep    = is_null( $dep ) ? [ 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-components' ] : (array) $dep;
		$handle = strtolower( 'gpersiandate-block-'.str_replace( '.', '-', $asset ) );

		wp_register_script( $handle, $base.$path.'/'.$asset.'/build/index.js', $dep, $version, TRUE );

		return $handle;
	}

	public static function registerBlockStyle( $asset, $dep = NULL, $version = GPERSIANDATE_VERSION, $base = GPERSIANDATE_URL, $path = 'assets/css' )
	{
		$dep    = is_null( $dep ) ? [] : (array) $dep;
		$handle = strtolower( 'gpersiandate-block-'.str_replace( '.', '-', $asset ) );

		wp_register_style( $handle, $base.$path.'/block.'.$asset.'.css', $dep, $version );

		return $handle;
	}

	public static function getResultsDB( $query, $output = OBJECT, $key = 'default', $group = 'gpersiandate' )
	{
		global $wpdb;

		$sub = md5( $query );

		$cache = wp_cache_get( $key, $group );

		if ( isset( $cache[$sub] ) )
			return $cache[$sub];

		$cache[$sub] = $wpdb->get_results( $query, $output );

		wp_cache_set( $key, $cache, $group );

		return $cache[$sub];
	}

	public static function hasPosts( $post_types = [ 'post' ], $exclude_statuses = NULL )
	{
		global $wpdb;

		return $wpdb->get_var( "
			SELECT 1 as test
			FROM {$wpdb->posts}
			WHERE post_type IN ( '".join( "', '", esc_sql( (array) $post_types ) )."' )
			AND post_status NOT IN ( '".join( "', '", esc_sql( self::getExcludeStatuses( $exclude_statuses ) ) )."' )
			LIMIT 1
		" );
	}

	public static function getExcludeStatuses( $statuses = NULL )
	{
		if ( is_null( $statuses ) )
			return [
				'draft',
				'private',
				'trash',
				'auto-draft',
				'inherit',
			];

		return (array) $statuses;
	}

	// @REF: http://php.net/manual/en/function.ob-start.php#71953
	// @REF: http://stackoverflow.com/a/6225706
	// @REF: https://coderwall.com/p/fatjmw/compressing-html-output-with-php
	public static function minifyHTML( $buffer )
	{
		$buffer = str_replace( [ "\n", "\r", "\t" ], '', $buffer );

		$buffer = preg_replace( [
			'/<!--(.*)-->/Uis',
			"/[[:blank:]]+/",
		], [
			'',
			' ',
		],
		$buffer );

		$buffer = preg_replace( [
			'/\>[^\S ]+/s', // strip whitespaces after tags, except space
			'/[^\S ]+\</s', // strip whitespaces before tags, except space
			'/(\s)+/s' // shorten multiple whitespace sequences
		], [
			'>',
			'<',
			'\\1'
		], $buffer );

		return trim( $buffer );
	}

	public static function prepTitle( $text, $post_id = 0 )
	{
		if ( ! $text )
			return '';

		$text = apply_filters( 'the_title', $text, $post_id );
		$text = apply_filters( 'string_format_i18n', $text );
		$text = apply_filters( 'gnetwork_typography', $text );

		return trim( $text );
	}

	public static function prepDescription( $text, $shortcode = TRUE, $autop = TRUE )
	{
		if ( ! $text )
			return '';

		if ( $shortcode )
			$text = do_shortcode( $text, TRUE );

		$text = apply_filters( 'html_format_i18n', $text );
		$text = apply_filters( 'gnetwork_typography', $text );

		return $autop ? wpautop( $text ) : $text;
	}
}
