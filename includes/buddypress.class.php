<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateBuddyPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		remove_filter( 'bp_get_total_group_count_for_user', 'bp_core_number_format' );
		remove_filter( 'bp_get_total_mention_count_for_user', 'bp_core_number_format' );
		remove_filter( 'bp_get_total_favorite_count_for_user', 'bp_core_number_format' );

		add_filter( 'bp_core_time_since', array( 'gPersianDateTranslate', 'numbers' ) );
		add_filter( 'bp_activity_get_comment_count', array( 'gPersianDateTranslate', 'numbers' ) );
		add_filter( 'bp_activity_recurse_comment_count', array( 'gPersianDateTranslate', 'numbers' ) );
	}
}
