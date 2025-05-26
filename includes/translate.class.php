<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateTranslate extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'number_format_i18n', [ __CLASS__, 'number_format_i18n' ], 10, 3 );

		// our filters!
		add_filter( 'number_format_i18n_back', [ __CLASS__, 'number_format_i18n_back' ] );
		add_filter( 'number_format_ordinal', [ __CLASS__, 'number_format_ordinal' ], 10, 3 );
		add_filter( 'number_format_words', [ __CLASS__, 'number_format_words' ], 10, 3 );
		add_filter( 'string_format_i18n', [ __CLASS__, 'legacy' ] ); // 'format_i18n'
		add_filter( 'string_format_i18n_back', [ __CLASS__, 'string_format_i18n_back' ] );
		add_filter( 'array_format_i18n', [ __CLASS__, 'array_map_numbers' ] );
		add_filter( 'array_format_i18n_back', [ __CLASS__, 'array_map_numbers_back' ] );
		add_filter( 'html_format_i18n', [ __CLASS__, 'html' ] );

		add_filter( 'maybe_format_i18n', [ __CLASS__, 'numbers' ], 10, 2 );

		add_filter( 'date_format_i18n', [ __CLASS__, 'date_format_i18n' ], 10, 5 );
		add_filter( 'date_format_i18n_back', [ __CLASS__, 'date_format_i18n_back' ], 10, 3 );
		add_filter( 'date_format_i18n_object', [ __CLASS__, 'date_format_i18n_object' ], 10, 3 );

		// FIXME: make optional
		add_filter( 'wp_insert_attachment_data', [ __CLASS__, 'attachment_data' ], 12, 2 );
		add_filter( 'image_add_caption_text', [ __CLASS__, 'html' ], 9 );
	}

	public static function date_format_i18n( $formatted, $format = NULL, $calendar = NULL, $timezone = NULL, $translate = NULL )
	{
		if ( ! $datetime = gPersianDateDate::getObject( $formatted, $calendar, $timezone ) )
			return $formatted;

		return gPersianDateDate::fromObject(
			gPersianDateFormat::sanitize( $format ?: '' ),
			$datetime,
			$timezone,
			NULL,
			$translate
		);
	}

	public static function date_format_i18n_back( $formatted, $format = NULL, $calendar = NULL, $timezone = NULL )
	{
		if ( ! $datetime = gPersianDateDate::getObject( $formatted, $calendar, $timezone ) )
			return $formatted;

		return $datetime->format( gPersianDateFormat::sanitize( $format ?: '' ) );
	}

	public static function date_format_i18n_object( $formatted, $calendar = NULL, $timezone = NULL )
	{
		return gPersianDateDate::getObject( $formatted, $calendar, $timezone );
	}

	public static function attachment_data( $data, $postarr )
	{
		// attachment title
		if ( ! empty( $data['post_title'] ) )
			$data['post_title'] = self::numbers( $data['post_title'] );

		// attachment caption
		if ( ! empty( $data['post_excerpt'] ) )
			$data['post_excerpt'] = self::html( $data['post_excerpt'] );

		// attachment description
		if ( ! empty( $data['post_content'] ) )
			$data['post_content'] = self::html( $data['post_content'] );

		return $data;
	}

	public static function format_i18n( $formatted, $decimals = 0 )
	{
		return self::numbers( $formatted );
	}

	public static function number_format_i18n( $formatted, $number = NULL, $decimals = 0 )
	{
		return self::numbers( ( ( is_null( $number ) || is_array( $number ) ) ? $formatted : number_format( $number, absint( $decimals ) ) ) );
	}

	public static function number_format_i18n_back( $formatted, $local = NULL )
	{
		return self::numbers_back( $formatted, $local, TRUE );
	}

	public static function number_format_ordinal( $ordinal, $number = NULL, $locale = NULL )
	{
		if ( is_null( $number ) )
			return $ordinal;

		switch ( self::sanitizeLocale( $locale ) ) {

			case 'en_US':
				return $ordinal; // TODO: support full ordinal in english

			case 'fa_IR':
				$numbers = new gPersianNumbersFA();
				return $numbers->number_to_ordinal( $number );
		}

		return $ordinal;
	}

	public static function number_format_words( $words, $number = NULL, $locale = NULL )
	{
		if ( is_null( $number ) )
			return $words;

		switch ( self::sanitizeLocale( $locale ) ) {

			case 'en_US':
				return gPersianNumbersEN::numberToWords( $number );

			case 'fa_IR':
				$numbers = new gPersianNumbersFA();
				return $numbers->number_to_words( $number );
		}

		return $words;
	}

	public static function string_format_i18n_back( $formatted, $local = NULL )
	{
		return self::numbers_back( $formatted, $local, FALSE );
	}

	public static function array_map_numbers( $array )
	{
		return array_map( [ __CLASS__, 'numbers' ], $array );
	}

	public static function array_map_numbers_back( $array )
	{
		return array_map( [ __CLASS__, 'numbers_back' ], $array );
	}

	public static function array_map_legacy( $array )
	{
		return array_map( [ __CLASS__, 'legacy' ], $array );
	}

	// adopted from core's make_clickable()
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

				// $ret = self::chars( preg_replace_callback( $pattern, [ __CLASS__, 'html_callback' ], $ret ) );
				$ret = preg_replace_callback( $pattern, [ __CLASS__, 'html_callback' ], $ret );

				$ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
				$r .= $ret;
			}
		}

		return $r;
	}

	public static function html_callback( $matches )
	{
		return isset( $matches[1] ) ? self::numbers( $matches[1] ) : $matches[0];
	}

	public static function legacy( $text )
	{
		if ( empty( $text ) || is_array( $text ) )
			return $text;

		//$pattern = '/(?:&#\d{2,4};)|((?:\&nbsp\;)*\d+(?:\&nbsp\;)*\d*\.*(?:\&nbsp\;)*\d*(?:\&nbsp\;)*\d*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i';
		$pattern = '/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/iu';

		return preg_replace_callback( $pattern, [ __CLASS__, 'html_callback' ], $text );
	}

	public static function sanitizeLocale( $locale = NULL )
	{
		if ( ! is_null( $locale ) )
			return $locale; // TODO: do the actual sanitization!

		if ( defined( 'GPERSIANDATE_LOCALE' ) )
			return constant( 'GPERSIANDATE_LOCALE' );

		return determine_locale();
	}

	// before: translate_numbers()
	public static function numbers( $string, $locale = NULL, $fix = NULL )
	{
		if ( ! is_numeric( $string ) && ! is_string( $string ) )
			return $string;

		if ( is_null( $string ) )
			return NULL;

		switch ( self::sanitizeLocale( $locale ) ) {

			case 'en_US':

				return self::chars( $string, FALSE );

			break;
			case 'ar':

				$string = strtr( $string, [
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

					'%' => chr(0xD9).chr(0xAA),
				] );

				return self::chars( $string, FALSE );

			break;
			case 'fa_IR':

				$string = strtr( $string, [
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

					'%' => chr(0xD9).chr(0xAA),
				] );

				return self::chars( $string, $fix );
		}

		return $string;
	}

	// @OLD: `translate_chars()`
	// @SEE: http://www.ltg.ed.ac.uk/~richard/utf-8.cgi
	public static function chars( $string, $fix = NULL )
	{
		if ( is_null( $fix ) )
			$fix = defined( 'GPERSIANDATE_FIXNONPERSIAN' )
				? constant( 'GPERSIANDATE_FIXNONPERSIAN' )
				: TRUE;

		if ( $fix ) {

			return strtr( $string, [

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
			] ) ;
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

	public static function numbers_back( $text, $locale = NULL, $intval = FALSE )
	{
		if ( is_null( $text ) )
			return NULL;

		switch ( self::sanitizeLocale( $locale ) ) {
			// case 'en_US':

			case 'fa_IR':

				$text = strtr( $text, [
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

					chr(0xD9).chr(0xAA) => '%',
				] );
		}

		if ( ! $intval )
			return $text;

		// FIXME: strip non numerial before intval
		return intval( $text );
	}
}
