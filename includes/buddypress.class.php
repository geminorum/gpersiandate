<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateBuddyPress extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_filter( 'bp_core_number_format', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'bp_core_time_since', [ 'gPersianDateTranslate', 'numbers' ] );

		add_filter( 'bp_groups_multiple_new_membership_requests_notification', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'bp_groups_multiple_membership_request_accepted_notification', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'bp_groups_multiple_membership_request_rejected_notification', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'bp_groups_multiple_member_promoted_to_admin_notification', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'bp_groups_multiple_member_promoted_to_mod_notification', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'bp_groups_multiple_group_invite_notification', [ 'gPersianDateTranslate', 'legacy' ] );
	}
}
