<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDatePlugins extends gPersianDateModuleCore
{

	protected function setup_actions()
	{
		// gShop
		add_filter( 'gshop_stats_current_month', array( $this, 'gshop_stats_current_month' ), 10, 3 );

		// gEditorial::Alphabets
		add_filter( 'geditorial_alphabet_strings', array( $this, 'geditorial_alphabet_strings' ), 8 );
	}

	public function gshop_stats_current_month( $month, $current, $force_iso )
	{
		return gPersianDateDate::to( 'Y_m', current_time( 'mysql' ) );
	}

	public function geditorial_alphabet_strings( $strings )
	{
		$new = array(
			'alphabet_fa_alef'  => 'ا',
			'alphabet_fa_be'    => 'ب',
			'alphabet_fa_pe'    => 'پ',
			'alphabet_fa_te'    => 'ت',
			'alphabet_fa_se'    => 'ث',
			'alphabet_fa_jim'   => 'ج',
			'alphabet_fa_che'   => 'چ',
			'alphabet_fa_hhe'   => 'ح',
			'alphabet_fa_khe'   => 'خ',
			'alphabet_fa_daal'  => 'د',
			'alphabet_fa_zaal'  => 'ذ',
			'alphabet_fa_re'    => 'ر',
			'alphabet_fa_ze'    => 'ز',
			'alphabet_fa_je'    => 'ژ',
			'alphabet_fa_sin'   => 'س',
			'alphabet_fa_shin'  => 'ش',
			'alphabet_fa_saad'  => 'ص',
			'alphabet_fa_zaad'  => 'ض',
			'alphabet_fa_taa'   => 'ط',
			'alphabet_fa_zaa'   => 'ظ',
			'alphabet_fa_ein'   => 'ع',
			'alphabet_fa_ghein' => 'غ',
			'alphabet_fa_fe'    => 'ف',
			'alphabet_fa_ghaaf' => 'ق',
			'alphabet_fa_kaaf'  => 'ک',
			'alphabet_fa_gaaf'  => 'گ',
			'alphabet_fa_laam'  => 'ل',
			'alphabet_fa_meem'  => 'م',
			'alphabet_fa_noon'  => 'ن',
			'alphabet_fa_vaav'  => 'و',
			'alphabet_fa_he'    => 'ه',
			'alphabet_fa_ye'    => 'ی',
		);

		// we do not need the english!!
		$strings['terms']['alphabet_tax'] = $new;
		return  $strings;
	}
}
