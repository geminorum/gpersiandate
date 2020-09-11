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
		if ( is_admin_bar_showing() && is_user_logged_in() )
			add_action( 'wp_before_admin_bar_render', [ $this, 'wp_before_admin_bar_render' ], 999 );
	}

	// needs to be last
	public function wp_before_admin_bar_render()
	{
		global $wp_admin_bar;

		$options = gPersianDate()->options();

		if ( empty( $options['adminbar_clock'] ) )
			return;

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

		$script = <<<'JS'
!function(n){function t(n){var t="۰".charCodeAt(0);return n.toString().replace(/\d+/g,function(n){return n.split("").map(function(n){return String.fromCharCode(t+parseInt(n))}).join("")})}function e(){var e=n("#gpd-now"),r=new Date,o=r.getHours(),a=r.getMinutes(),u=r.getSeconds();o=(o<10?"0":"")+o,a=(a<10?"0":"")+a,u=(u<10?"0":"")+u;var c=o+":"+a+":"+u;"fa_IR"===e.data("locale")&&(c=t(c)),e.html(c)}n(document).ready(function(){setInterval(e,1e3)})}(jQuery);
JS;

		// @REF: https://wordpress.stackexchange.com/a/311279
		wp_register_script( 'gperdiandate-clock', '', [ 'jquery' ], '', TRUE );
		wp_enqueue_script( 'gperdiandate-clock'  ); // must register then enqueue
		wp_add_inline_script( 'gperdiandate-clock', $script );

		// NOTE: for reference
		// wp_enqueue_script( 'gperdiandate-clock', GPERSIANDATE_URL.'assets/js/adminbar.clock.min.js', [ 'jquery' ], GPERSIANDATE_VERSION, TRUE );
	}
}
