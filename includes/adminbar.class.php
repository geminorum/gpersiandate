<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateAdminBar extends gPersianDateModuleCore
{

	private $adminbar = FALSE;

	protected function setup_actions()
	{
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init()
	{
		if ( is_admin_bar_showing() && is_user_logged_in() ) {

			$this->_options = gPersianDate()->options();
			$this->adminbar = isset( $this->_options['adminbar_clock'] ) && $this->_options['adminbar_clock'];

			if ( ! $this->adminbar )
				return;

			add_action( 'wp_before_admin_bar_render', [ $this, 'wp_before_admin_bar_render' ], 999 );

			// http://www.jquery4u.com/snippets/create-jquery-digital-clock-jquery4u/
			wp_register_script( 'gperdiandate-clock',
				GPERSIANDATE_URL.'assets/js/adminbar.clock.min.js',
				[ 'jquery' ],
				GPERSIANDATE_VERSION,
				TRUE
			);

			add_action( ( is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts' ), function(){
				wp_enqueue_script( 'gperdiandate-clock' );
			});
		}
	}

	// needs to be last
	public function wp_before_admin_bar_render()
	{
		global $wp_admin_bar;

		if ( is_rtl() )
			$title = '<span id="gpd-now" data-locale="'.GPERSIANDATE_LOCALE.'">'
				.date_i18n( 'H:i:s' ).'</span> - <span id="gpd-today">'
				.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span>';

		else
			$title = '<span id="gpd-today">'
				.date_i18n( get_option( 'date_format', 'j M Y' ) )
				.'</span> - <span id="gpd-now" data-locale="'.GPERSIANDATE_LOCALE.'">'
				.date_i18n( 'H:i:s' ).'</span>';

		$wp_admin_bar->add_node( [
			'parent' => 'top-secondary',
			'id'     => 'gpersiandate',
			'title'  => $title,
			'href'   => current_user_can( 'manage_options' ) ? get_admin_url( NULL, 'options-general.php' ) : FALSE,
		] );
	}
}
