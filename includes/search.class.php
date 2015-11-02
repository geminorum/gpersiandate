<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateSearch extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'posts_request', array( $this, 'posts_request' ), 20 );
		add_filter( 'posts_search', array( $this, 'posts_request' ), 20 );

		add_filter( 'get_search_query', array( 'gPersianDateTranslate', 'chars' ) );
	}

	// Originally from wp-jalali
	public function posts_request( $query )
	{
		if ( strstr( $query, 'LIKE' ) )
			if ( strstr( $query, "ی" )
				|| strstr( $query, "ک" )
				|| strstr( $query, "ي" )
				|| strstr( $query, "ك" )
				|| strstr( $query, "٤" )
				|| strstr( $query, "٥" )
				|| strstr( $query, "٦")
				|| strstr( $query, "۴" )
				|| strstr( $query, "۵" )
				|| strstr( $query, "۶" ) )
					$query = preg_replace_callback( "/(\([^\)\(]* LIKE '([^']*)'\))/",
						array( __CLASS__, 'posts_request_callback' ), $query );
		return $query;
	}

	// Originally from wp-jalali
	public static function posts_request_callback( $matches )
	{
		return
			"( ".
			str_replace(
				$matches[2],
				str_replace(
					array( "ي", "ك", "٤", "٥", "٦" ),
					array( "ی", "ک", "۴", "۵", "۶" ),
					$matches[2]
				),
				$matches[1]
			)." OR ".
			str_replace(
				$matches[2],
				str_replace(
					array( "ی", "ک", "۴", "۵", "۶" ),
					array( "ي", "ك", "٤", "٥", "٦" ),
					$matches[2]
				), $matches[1]
			)." )";
	}
}
