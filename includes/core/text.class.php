<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateText
{

	public static function has( $haystack, $needles )
	{
		if ( ! is_array( $needles ) )
			return FALSE !== stripos( $haystack, $needles );

		foreach ( $needles as $needle )
			if ( FALSE !== stripos( $haystack, $needle ) )
				return TRUE;

		return FALSE;
	}
}
