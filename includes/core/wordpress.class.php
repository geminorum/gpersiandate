<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateWP
{

	// @REF: `vars.php`
	public static function pageNow()
	{
		if ( preg_match( '#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $matches ) )
			return strtolower( $matches[1] );

		return 'index.php';
	}
}
