<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateBBPress extends gPersianDateModuleCore
{
	var $_ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'bbp_number_format', array( 'gPersianDateTranslate', 'numbers' ), 12 );
		add_filter( 'bbp_get_time_since', array( 'gPersianDateTranslate', 'numbers' ), 12 );
	}
}
