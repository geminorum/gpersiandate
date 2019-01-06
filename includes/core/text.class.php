<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateText
{

	public static function has( $haystack, $needles, $operator = 'OR' )
	{
		if ( ! is_array( $needles ) )
			return FALSE !== stripos( $haystack, $needles );

		if ( 'OR' == $operator ) {
			foreach ( $needles as $needle )
				if ( FALSE !== stripos( $haystack, $needle ) )
					return TRUE;

			return FALSE;
		}

		$has = FALSE;

		foreach ( $needles as $needle )
			if ( FALSE !== stripos( $haystack, $needle ) )
				$has = TRUE;

		return $has;
	}

	// @SOURCE: http://bavotasan.com/2012/trim-characters-using-php/
	public static function trimChars( $text, $length = 45, $append = '&hellip;' )
	{
		$length = (int) $length;
		$text   = trim( strip_tags( $text ) );

		if ( strlen( $text ) > $length ) {

			$text  = substr( $text, 0, $length + 1 );
			$words = preg_split( "/[\s]|&nbsp;/", $text, -1, PREG_SPLIT_NO_EMPTY );

			preg_match( "/[\s]|&nbsp;/", $text, $lastchar, 0, $length );

			if ( empty( $lastchar ) )
				array_pop( $words );

			$text = implode( ' ', $words ).$append;
		}

		return $text;
	}
}
