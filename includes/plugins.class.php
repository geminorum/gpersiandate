<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePlugins extends gPersianDateModuleCore
{

	protected $ajax = TRUE;

	protected function setup_actions()
	{
		add_action( 'bp_include', [ $this, 'bp_include' ] ); // BuddyPress
		add_action( 'bbp_includes', [ $this, 'bbp_includes' ] ); // bbPress
		add_action( 'woocommerce_loaded', [ $this, 'woocommerce_loaded' ] ); // WooCommerce

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

		// add_filter( 'bp_core_time_since', [ 'gPersianDateTranslate', 'numbers' ] );
		// add_filter( 'bp_groups_multiple_new_membership_requests_notification', [ 'gPersianDateTranslate', 'legacy' ] );
	}

	public function bbp_includes()
	{
		add_filter( 'bbp_number_format', [ 'gPersianDateTranslate', 'numbers' ], 12 );
		add_filter( 'bbp_get_time_since', [ 'gPersianDateTranslate', 'numbers' ], 12 );

		add_filter( 'bbp_number_format_i18n', [ $this, 'bbp_number_format_i18n' ], 12, 3 );
	}

	public function woocommerce_loaded()
	{
		add_filter( 'woocommerce_get_country_locale', [ $this, 'woocommerce_get_country_locale' ] );

		// messes up the postcodes!
		// add_filter( 'woocommerce_format_postcode', [ 'gPersianDateTranslate', 'numbers_back' ], 5 );
		// add_filter( 'woocommerce_format_postcode', [ 'gPersianDateTranslate', 'numbers' ], 15 );

		add_filter( 'formatted_woocommerce_price', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'woocommerce_order_item_quantity_html', [ 'gPersianDateTranslate', 'legacy' ] );
		add_filter( 'woocommerce_checkout_cart_item_quantity', [ 'gPersianDateTranslate', 'numbers' ] );

		add_filter( 'woocommerce_product_get_review_count', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'woocommerce_order_get_quantity', [ 'gPersianDateTranslate', 'numbers' ] );

		add_filter( 'woocommerce_format_weight', [ 'gPersianDateTranslate', 'numbers' ] );
		add_filter( 'woocommerce_format_dimensions', [ 'gPersianDateTranslate', 'numbers' ] );
		// add_filter( 'woocommerce_format_localized_decimal', [ $this, 'wc_format_localized_decimal' ], 9, 2 );

		add_filter( 'woocommerce_localisation_address_formats', [ $this, 'wc_localisation_address_formats' ] );
		add_filter( 'woocommerce_formatted_address_replacements', [ $this, 'wc_formatted_address_replacements' ], 9 );
		add_filter( 'woocommerce_formatted_address_force_country_display', '__return_true' );

		add_filter( 'woocommerce_subcategory_count_html', [ 'gPersianDateTranslate', 'legacy' ] );

		// add_filter( 'woocommerce_sale_price_html', [ 'gPersianDateTranslate', 'legacy' ] );
		// add_filter( 'woocommerce_price_html', [ 'gPersianDateTranslate', 'legacy' ] );

		// messes with other jquery-ui components
		// add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts_woocommerce' ], 20 );

		// add_filter( 'woocommerce_date_input_html_pattern', [ $this, 'woocommerce_date_input_html_pattern' ] );

		// no need
		// add_filter( 'woocommerce_currencies', [ $this, 'woocommerce_currencies' ] );
		// add_filter( 'woocommerce_currency_symbol', [ $this, 'woocommerce_currency_symbol' ], 10, 2 );
	}

	public function bbp_number_format_i18n( $formatted, $number, $decimals )
	{
		return $number; // avoid double convertion
	}

	public function admin_enqueue_scripts_woocommerce()
	{
		if ( defined( 'GPERSIANDATE_DISABLE_DATEPICKER' ) && GPERSIANDATE_DISABLE_DATEPICKER )
			return;

		wp_deregister_style( 'jquery-ui-style' );
		wp_register_style( 'jquery-ui-style', '' );
	}

	public function woocommerce_get_country_locale( $locales )
	{
		$customized = [
			'postcode' => [
				'priority' => 65,
			],
			'country' => [
				'label' => _x( 'Country', 'Plugins: Woocommerce: Locales', 'gpersiandate' ),
			],
			'city' => [
				'label' => _x( 'City', 'Plugins: Woocommerce: Locales', 'gpersiandate' ),
			],
			'state' => [
				'label' => _x( 'Province', 'Plugins: Woocommerce: Locales', 'gpersiandate' ),
			],
		];

		if ( array_key_exists( 'IR', $locales ) )
			$locales['IR'] = array_merge( $locales['IR'], $customized );

		else
			$locales['IR'] = $customized;

		return $locales;
	}

	// DEFAULT: `[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])`
	public function woocommerce_date_input_html_pattern( $pattern )
	{
		return ''; // $pattern;
	}

	public function woocommerce_currencies( $currencies )
	{
		return array_merge( $currencies, [ 'TOMAN' => _x( 'Toman', 'WooCommerce: Currencies', 'gpersiandate' ) ] );
	}

	public function woocommerce_currency_symbol( $currency_symbol, $currency )
	{
		return 'TOMAN' == $currency ? _x( 'Toman', 'WooCommerce: Currencies', 'gpersiandate' ) : $currency_symbol;
	}

	public function wc_localisation_address_formats( $formats )
	{
		$formats['IR'] = "{name}\n{company}\n {country}، {state}، {city}\n{address_1}\n{address_2}\n{postcode}";

		return $formats;
	}

	public function wc_formatted_address_replacements( $replace )
	{
		$replace['{address_1}'] = gPersianDateTranslate::numbers( $replace['{address_1}'] );
		$replace['{address_2}'] = gPersianDateTranslate::numbers( $replace['{address_2}'] );
		$replace['{postcode}']  = gPersianDateTranslate::numbers( $replace['{postcode}'] );

		return $replace;
	}

	public function gshop_stats_current_month( $month, $current, $force_iso )
	{
		return gPersianDateDate::fromObject( 'Y_m' );
	}
}
