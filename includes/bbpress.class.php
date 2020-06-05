<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateBBPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'bbp_number_format', [ 'gPersianDateTranslate', 'numbers' ], 12 );
		add_filter( 'bbp_get_time_since', [ 'gPersianDateTranslate', 'numbers' ], 12 );

		add_filter( 'bbp_number_format_i18n', [ $this, 'number_format_i18n' ], 12, 3 );
	}

	public function number_format_i18n( $formatted, $number, $decimals )
	{
		return $number; // avoid double convertion
	}
}
