<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateBuddyPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'bp_get_total_group_count_for_user', [ 'gPersianDateTranslate', 'numbers' ] );

		add_filter( 'bp_core_time_since', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'bp_activity_get_comment_count', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'bp_activity_recurse_comment_count', [ 'gPersianDateTranslate', 'numbers' ] );
	}
}
