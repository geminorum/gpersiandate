<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

/*
Plugin Name: gPersianDate
Plugin URI: https://geminorum.ir/wordpress/gpersiandate
Update URI: https://github.com/geminorum/gpersiandate
Description: Persian Date for WordPress
Version: 3.8.1
License: GPLv3+
Author: geminorum
Author URI: https://geminorum.ir/
Network: false
TextDomain: gpersiandate
Domain Path: /languages
Requires PHP: 5.6.20
Requires WP: 5.0
Requires at least: 5.0
GitHub Plugin URI: https://github.com/geminorum/gpersiandate
*/

define( 'GPERSIANDATE_VERSION', '3.8.1' );
define( 'GPERSIANDATE_MIN_PHP', '5.6.20' );
define( 'GPERSIANDATE_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPERSIANDATE_URL', plugin_dir_url( __FILE__ ) );
define( 'GPERSIANDATE_FILE', basename( GPERSIANDATE_DIR ).'/'.basename( __FILE__ ) );

// defined( 'GPERSIANDATE_TEXTDOMAIN' ) or define( 'GPERSIANDATE_TEXTDOMAIN', 'gpersiandate' );

if ( version_compare( GPERSIANDATE_MIN_PHP, phpversion(), '>=' ) ) {

	if ( is_admin() ) {
		echo '<div class="notice notice-warning notice-alt is-dismissible"><p dir="ltr">';
			printf( '<b>gPersianDate</b> requires PHP %s or higher. Please contact your hosting provider to update your site.', GPERSIANDATE_MIN_PHP ) ;
		echo '</p></div>';
	}

	return FALSE;

} else {

	function gpersiandate_init() {

		$includes = [
			'core/base',
			'core/html',
			'core/text',
			'core/wordpress',
			'utilities',

			'core',
			'modulecore',
			'datetime',
			'format',
			'strings',
			'translate',
			'timezone',
			'search',
			'links',
			'admin',
			'archives',
			'wordpress',
			'adminbar',
			'shortcodes',
			'date',
			'calendar',
			'plugins',
			'form',

			'misc/numbers.en',
			'misc/numbers.fa',

			'widgets/archives',
			'widgets/calendar',

			'picker',
			'timeago',
		];

		foreach ( $includes as $include )
			if ( file_exists( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' ) )
				require_once( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' );

		gPersianDate();
	}

	function gPersianDate() {
		return gPersianDateCore::instance();
	}

	gpersiandate_init();
}
