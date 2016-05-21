<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePlugins extends gPersianDateModuleCore
{

	protected function setup_actions()
	{
		// gShop
		add_filter( 'gshop_stats_current_month', array( $this, 'gshop_stats_current_month' ), 10, 3 );
	}

	public function gshop_stats_current_month( $month, $current, $force_iso )
	{
		return gPersianDateDate::to( 'Y_m', current_time( 'mysql' ) );
	}
}
