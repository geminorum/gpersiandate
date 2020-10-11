<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePlugins extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_action( 'bp_include', [ $this, 'bp_include' ] ); // BuddyPress
		add_action( 'bbp_includes', [ $this, 'bbp_includes' ] ); // bbPress

		// gShop
		add_filter( 'gshop_stats_current_month', [ $this, 'gshop_stats_current_month' ], 10, 3 );
	}

	public function bp_include()
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

	public function bbp_includes()
	{
		add_filter( 'bbp_number_format', [ 'gPersianDateTranslate', 'numbers' ], 12 );
		add_filter( 'bbp_get_time_since', [ 'gPersianDateTranslate', 'numbers' ], 12 );

		add_filter( 'bbp_number_format_i18n', [ $this, 'bbp_number_format_i18n' ], 12, 3 );
	}

	public function bbp_number_format_i18n( $formatted, $number, $decimals )
	{
		return $number; // avoid double convertion
	}

	public function gshop_stats_current_month( $month, $current, $force_iso )
	{
		return gPersianDateDate::fromObject( 'Y_m' );
	}
}
