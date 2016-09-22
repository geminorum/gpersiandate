<?php defined( 'ABSPATH' ) or die( 'Restricted access' );
/*
Plugin Name: gPersianDate
Plugin URI: https://geminorum.ir/wordpress/gpersiandate
Description: Persian Date for WordPress
License: GPLv3+
Author: geminorum
Version: 3.4.1
Author URI: http://geminorum.ir/
GitHub Plugin URI: https://github.com/geminorum/gpersiandate
GitHub Branch: master
Requires WP: 4.4
Requires PHP: 5.3
*/

/*
	Copyright 2016 geminorum

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'GPERSIANDATE_VERSION', '3.4.1' );
define( 'GPERSIANDATE_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPERSIANDATE_URL', plugin_dir_url( __FILE__ ) );
define( 'GPERSIANDATE_FILE', __FILE__ );
defined( 'GPERSIANDATE_TEXTDOMAIN' ) or define( 'GPERSIANDATE_TEXTDOMAIN', 'gpersiandate' );

function gpersiandate_init(){

	$includes = array(
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
		'html',
	);

	foreach ( $includes as $include )
		if ( file_exists( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' ) )
			require_once( GPERSIANDATE_DIR.'includes/'.$include.'.class.php' );

	defined( 'GPERSIANDATE_TIMEZONE' ) or define( 'GPERSIANDATE_TIMEZONE', gPersianDateTimeZone::current() );
	defined( 'GPERSIANDATE_LOCALE' ) or define( 'GPERSIANDATE_LOCALE', get_locale() );
	defined( 'GPERSIANDATE_FIXNONPERSIAN' ) or define( 'GPERSIANDATE_FIXNONPERSIAN', TRUE );

	// add_action( 'plugins_loaded', 'gPersianDate' );
	gPersianDate();
}

function gPersianDate() {
	return gPersianDateCore::instance();
}

gpersiandate_init();
