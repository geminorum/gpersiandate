<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateTranslate extends gPersianDateModuleCore
{

	var $_ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'number_format_i18n', array( &$this, 'format_i18n' ), 10, 2 );
		add_filter( 'bb_number_format_i18n', array( &$this, 'format_i18n' ), 10, 1 );

		// our filters!
		add_filter( 'number_format_i18n_back', array( &$this, 'format_i18n_back' ) );
		add_filter( 'string_format_i18n', array( &$this, 'format_i18n' ) );
		add_filter( 'html_format_i18n', array( 'gPersianDateTranslate', 'html' ) );

		add_filter( 'maybe_format_i18n', array( 'gPersianDateTranslate', 'numbers' ), 10, 2 );
	}

	public function format_i18n( $formatted, $decimals = 0 )
	{
		return self::numbers( $formatted );
	}

	public function format_i18n_back( $formatted, $local = GPERSIANDATE_LOCALE  )
	{
		return self::numbers_back( $formatted, $local );
	}

	// adopt from core's make_clickable()
	public static function html( $text )
	{
		$r = '';
		$textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
		$nested_code_pre = 0; // Keep track of how many levels link is nested inside <pre> or <code>

		foreach ( $textarr as $piece ) {

			if ( preg_match( '|^<code[\s>]|i', $piece ) || preg_match( '|^<pre[\s>]|i', $piece ) )
				$nested_code_pre++;
			elseif ( ( '</code>' === strtolower( $piece ) || '</pre>' === strtolower( $piece ) ) && $nested_code_pre )
				$nested_code_pre--;

			if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
				$r .= $piece;
				continue;
			}

			// Long strings might contain expensive edge cases ...
			if ( 10000 < strlen( $piece ) ) {
				// ... break it up
				foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
					if ( 2101 < strlen( $chunk ) ) {
						$r .= $chunk; // Too big, no whitespace: bail.
					} else {
						$r .= self::html( $chunk );
					}
				}
			} else {
				$ret = " $piece "; // Pad with whitespace to simplify the regexes

				//$pattern = '/(?:&#\d{2,4};)|((?:\&nbsp\;)*\d+(?:\&nbsp\;)*\d*\.*(?:\&nbsp\;)*\d*(?:\&nbsp\;)*\d*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i';
				$pattern = '/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/iu';
				// $ret = self::chars( preg_replace_callback( $pattern, array( __CLASS__, 'html_callback' ), $ret ) );
				$ret = preg_replace_callback( $pattern, array( __CLASS__, 'html_callback' ), $ret );

				$ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
				$r .= $ret;
			}
		}

		return $r;
	}

	public static function html_callback( $matches )
	{
		if ( isset( $matches[1] ) )
			return self::numbers( $matches[1] );
		else
			return $matches[0];
	}

	public static function legacy( $text )
	{
		//$pattern = '/(?:&#\d{2,4};)|((?:\&nbsp\;)*\d+(?:\&nbsp\;)*\d*\.*(?:\&nbsp\;)*\d*(?:\&nbsp\;)*\d*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i';
		$pattern = '/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/iu';

		return preg_replace_callback( $pattern, array( __CLASS__, 'html_callback' ), $text );
	}

	// before: translate_numbers()
	public static function numbers( $string, $local = GPERSIANDATE_LOCALE )
	{
		if ( is_null( $string ) )
			return NULL;

		switch( $local ) {

			case 'en_US' :

				return self::chars( $string, FALSE );

			break;
			case 'ar' :

				$string = strtr( $string, array(
					'0' => chr(0xD9).chr(0xA0),
					'1' => chr(0xD9).chr(0xA1),
					'2' => chr(0xD9).chr(0xA2),
					'3' => chr(0xD9).chr(0xA3),
					'4' => chr(0xD9).chr(0xA4),
					'5' => chr(0xD9).chr(0xA5),
					'6' => chr(0xD9).chr(0xA6),
					'7' => chr(0xD9).chr(0xA7),
					'8' => chr(0xD9).chr(0xA8),
					'9' => chr(0xD9).chr(0xA9),
				) );

				return self::chars( $string, FALSE );

			break;
			case 'fa_IR' :

				$string = strtr( $string, array(
					'0' => chr(0xDB).chr(0xB0),
					'1' => chr(0xDB).chr(0xB1),
					'2' => chr(0xDB).chr(0xB2),
					'3' => chr(0xDB).chr(0xB3),
					'4' => chr(0xDB).chr(0xB4),
					'5' => chr(0xDB).chr(0xB5),
					'6' => chr(0xDB).chr(0xB6),
					'7' => chr(0xDB).chr(0xB7),
					'8' => chr(0xDB).chr(0xB8),
					'9' => chr(0xDB).chr(0xB9),
				) );

				return self::chars( $string );
		}

		return $string;
	}

	// before: translate_chars()
	// http://www.ltg.ed.ac.uk/~richard/utf-8.cgi
	public static function chars( $string, $fix = GPERSIANDATE_FIXNONPERSIAN )
	{
		if ( $fix ) {

			return strtr( $string, array(

				chr(0xD9).chr(0xA0) => chr(0xDB).chr(0xB0),
				chr(0xD9).chr(0xA1) => chr(0xDB).chr(0xB1),
				chr(0xD9).chr(0xA2) => chr(0xDB).chr(0xB2),
				chr(0xD9).chr(0xA3) => chr(0xDB).chr(0xB3),
				chr(0xD9).chr(0xA4) => chr(0xDB).chr(0xB4),
				chr(0xD9).chr(0xA5) => chr(0xDB).chr(0xB5),
				chr(0xD9).chr(0xA6) => chr(0xDB).chr(0xB6),
				chr(0xD9).chr(0xA7) => chr(0xDB).chr(0xB7),
				chr(0xD9).chr(0xA8) => chr(0xDB).chr(0xB8),
				chr(0xD9).chr(0xA9) => chr(0xDB).chr(0xB9),

				chr(0xD9).chr(0x83) => chr(0xDA).chr(0xA9), // ARABIC LETTER KAF > ARABIC LETTER KEHEH
				chr(0xD9).chr(0x89) => chr(0xDB).chr(0x8C), // ARABIC LETTER ALEF MAKSURA > ARABIC LETTER FARSI YEH
				chr(0xD9).chr(0x8A) => chr(0xDB).chr(0x8C), // ARABIC LETTER YEH > ARABIC LETTER FARSI YEH
				chr(0xDB).chr(0x80) => chr(0xD9).chr(0x87).chr(0xD9).chr(0x94),

				// http://stackoverflow.com/a/13481824
				// chr(0xE2).chr(0x80).chr(0x8C), // ZERO WIDTH NON-JOINER (U+200C) : &zwnj;

			) ) ;
		}

		return $string;
	}

	public static function format( $format = '' )
	{
		// TODO : apply filters

		if ( 'M j, Y @ G:i' == $format && 'fa_IR' == constant( 'GPERSIANDATE_LOCALE' ) )
			return 'j M Y @ G:i';

			// 'Y/m/d g:i:s A'

		return $format;
	}

	public static function numbers_back( $text, $local = GPERSIANDATE_LOCALE )
	{
		if ( is_null( $text ) )
			return NULL;

		switch( $local ) {
			// case 'en_US' :

			case 'fa_IR' :

				$text = strtr( $text, array(
					chr(0xDB).chr(0xB0) => '0',
					chr(0xDB).chr(0xB1) => '1',
					chr(0xDB).chr(0xB2) => '2',
					chr(0xDB).chr(0xB3) => '3',
					chr(0xDB).chr(0xB4) => '4',
					chr(0xDB).chr(0xB5) => '5',
					chr(0xDB).chr(0xB6) => '6',
					chr(0xDB).chr(0xB7) => '7',
					chr(0xDB).chr(0xB8) => '8',
					chr(0xDB).chr(0xB9) => '9',
				) );

			break;

		}

		// todo : strip non numerial
		return intval( $text );
	}
}