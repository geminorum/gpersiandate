<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

/*
Plugin Name: gPersianDate
Plugin URI: https://geminorum.ir/wordpress/gpersiandate
Description: Persian Date for WordPress
Version: 3.5.1
License: GPLv3+
Author: geminorum
Author URI: http://geminorum.ir/
Network: false
GitHub Plugin URI: https://github.com/geminorum/gpersiandate
GitHub Branch: master
Requires WP: 4.4
Requires PHP: 5.3
*/

define( 'GPERSIANDATE_VERSION', '3.5.1' );
define( 'GPERSIANDATE_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPERSIANDATE_URL', plugin_dir_url( __FILE__ ) );
define( 'GPERSIANDATE_FILE', basename( GPERSIANDATE_DIR ).'/'.basename( __FILE__ ) );

function gpersiandate_init(){

	$includes = array(
		'core/base',
		'core/html',
		'core/text',
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
		'buddypress',
		'bbpress',
		'widgets',
		'date',
		'calendar',
		'plugins',
		'form',

		'picker',
	);

	foreach ( $includes as $include )
		if ( file_exists( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' ) )
			require_once( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' );

	defined( 'GPERSIANDATE_TEXTDOMAIN' ) or define( 'GPERSIANDATE_TEXTDOMAIN', 'gpersiandate' );
	defined( 'GPERSIANDATE_TIMEZONE' ) or define( 'GPERSIANDATE_TIMEZONE', gPersianDateTimeZone::current() );
	defined( 'GPERSIANDATE_LOCALE' ) or define( 'GPERSIANDATE_LOCALE', get_locale() );
	defined( 'GPERSIANDATE_FIXNONPERSIAN' ) or define( 'GPERSIANDATE_FIXNONPERSIAN', TRUE );

	gPersianDate();
}

function gPersianDate() {
	return gPersianDateCore::instance();
}

gpersiandate_init();
