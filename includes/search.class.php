<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateSearch extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'posts_search', [ $this, 'posts_search' ], 20, 2 );

		add_filter( 'get_search_query', [ 'gPersianDateTranslate', 'chars' ] );
	}

	// TODO: english numbers too?
	protected static function chars()
	{
		return [
			'ي' => 'ی',
			'ك' => 'ک',
			'٠' => '۰',
			'١' => '۱',
			'٢' => '۲',
			'٣' => '۳',
			'٤' => '۴',
			'٥' => '۵',
			'٦' => '۶',
			'٧' => '۷',
			'٨' => '۸',
			'٩' => '۹',
		];
	}

	public function posts_search( $search, $wp_query )
	{
		if ( ! gPersianDateText::has( $search, 'LIKE' ) )
			return $search;

		$chars = self::chars();

		if ( ! gPersianDateText::has( $search, array_keys( $chars ) + $chars ) )
			return $search;

		return preg_replace_callback( "/(\([^\)\(]* LIKE '([^']*)' ?\))/",
			[ __CLASS__, 'duplicateClause' ], $search );
	}

	public static function duplicateClause( $matches )
	{
		$chars   = self::chars();
		$join    = gPersianDateText::has( $matches[1], 'NOT LIKE' ) ? ' AND ' : ' OR ';
		$persian = str_replace( array_keys( $chars ), array_values( $chars ), $matches[2] );
		$arabic  = str_replace( array_values( $chars ), array_keys( $chars ), $matches[2] );

		return '('.str_replace( $matches[2], $persian, $matches[1] )
			.$join.str_replace( $matches[2], $arabic, $matches[1] ).')';
	}
}
