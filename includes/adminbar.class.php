<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateAdminBar extends gPersianDateModuleCore
{

	var $_adminbar = FALSE;

	protected function setup_actions()
	{
		add_action( 'init', array( &$this, 'init' ) );
	}

	public function init()
	{
		if ( is_admin_bar_showing() && is_user_logged_in() ) {

			$this->_options = gPersianDate()->options();
			$this->_adminbar = isset( $this->_options['adminbar_clock'] ) && $this->_options['adminbar_clock'];

			if ( ! $this->_adminbar )
				return;

			add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ) );

			// http://www.jquery4u.com/snippets/create-jquery-digital-clock-jquery4u/
			wp_register_script( 'gperdiandate-clock',
				GPERSIANDATE_URL.'assets/js/adminbar.clock.min.js',
				array( 'jquery' ),
				GPERSIANDATE_VERSION,
				true
			);

			wp_localize_script( 'gperdiandate-clock',
				'GPD_clock', array(
					'local' => GPERSIANDATE_LOCALE,
			) );

			add_action( ( is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts' ), function(){
				wp_enqueue_script( 'gperdiandate-clock' );
			});
		}
	}

	public function admin_bar_menu( $wp_admin_bar )
	{
		if ( is_rtl() )
			$title = '<span id="gpd-now">'.date_i18n( 'H:i' ).'</span> - <span id="gpd-today">'.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span>';
		else
			$title = '<span id="gpd-today">'.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span> - <span id="gpd-now">'.date_i18n( 'H:i' ).'</span>';

		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate',
			'title'  => $title,
			'parent' => 'top-secondary', // Off on the right side
			'href'   => ( current_user_can( 'manage_options' ) ? get_admin_url( null, 'options-general.php' ) : false ),
		) );

		return;

		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate-today',
			'title'  => esc_html( date_i18n( get_option( 'date_format', 'j M Y' ) ) ),
			'parent' => 'top-secondary',
			'href'   => false,
			'meta'   => array(
				'title'  => ( is_admin() ? esc_html__( 'Today in Persian ( just to make sure the conversion is intact )', GPERSIANDATE_TEXTDOMAIN ) : esc_html__( 'Today in Persian', GPERSIANDATE_TEXTDOMAIN ) ),
			),
		) );

		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate-now',
			'title'  => esc_html( date_i18n( 'H:i' ) ), // get_option( 'time_format', 'g:i A' )
			'parent' => 'top-secondary',
			'href'   => false,
			'meta'   => array(
				'title'  => ( is_admin() ? esc_html__( 'Now ( just to make sure time zone is correct )', GPERSIANDATE_TEXTDOMAIN ) : esc_html__( 'Just Now', GPERSIANDATE_TEXTDOMAIN ) ),
			),
		) );
	}
}
