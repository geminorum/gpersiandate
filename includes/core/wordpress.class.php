<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateWP
{

	// Checks compatibility with the current WordPress version.
	// @REF: `is_wp_version_compatible()`
	public static function isWPcompatible( $required )
	{
		return empty( $required ) || version_compare( $GLOBALS['wp_version'], $required, '>=' );
	}

	// @REF: `vars.php`
	// TODO: support arrays
	public static function pageNow( $page = NULL )
	{
		$now = 'index.php';

		if ( preg_match( '#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $matches ) )
			$now = strtolower( $matches[1] );

		return is_null( $page ) ? $now : ( $now == $page );
	}
}
