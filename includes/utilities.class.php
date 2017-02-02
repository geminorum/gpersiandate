<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateUtilities extends gPersianDateBase
{

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

	// @REF: http://php.net/manual/en/function.ob-start.php#71953
	// @REF: http://stackoverflow.com/a/6225706
	public static function minifyHTML( $buffer )
	{
		return trim( preg_replace( array(
			'/\>[^\S ]+/s', // strip whitespaces after tags, except space
			'/[^\S ]+\</s', // strip whitespaces before tags, except space
			'/(\s)+/s' // shorten multiple whitespace sequences
		), array(
			'>',
			'<',
			'\\1'
		), $buffer ) );
	}
}
