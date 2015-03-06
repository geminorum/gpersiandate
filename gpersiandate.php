<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

/*
Plugin Name: gPersianDate
Plugin URI: https://github.com/geminorum/gpersiandate
Description: Persian Date for WordPress. Using <a href="https://github.com/farhadi/ExtDateTime" title="An extented version of php5 DateTime Class that adds some more functionality to it and makes it extensible for other calendar systems.">ExtDateTime</a> by <a href="http://farhadi.ir/">Ali Farhadi</a>. PHP 5.2 required.
Author: geminorum
Version: 0.3.0
Author URI: http://geminorum.ir/
GitHub Plugin URI: https://github.com/geminorum/gpersiandate
GitHub Branch: master
*/

/**
	Copyright 2015 geminorum

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
**/

define( 'GPERSIANDATE_VERSION', '0.3.0' );
define( 'GPERSIANDATE_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPERSIANDATE_URL', plugin_dir_url( __FILE__ ) );
define( 'GPERSIANDATE_FILE', __FILE__ );
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
defined( 'GPERSIANDATE_TEXTDOMAIN' ) or define( 'GPERSIANDATE_TEXTDOMAIN', 'gpersiandate' );

function gpersiandate_init(){
	
	$includes = array(
		'core',
		'modulecore',
		
		'date',
	);
	
	foreach ( $includes as $include )
		if ( file_exists( GPERSIANDATE_DIR.'includes'.DS.$include.'.class.php' ) ) 
			require_once( GPERSIANDATE_DIR.'includes'.DS.$include.'.class.php' );

	//add_action( 'plugins_loaded', 'gPersianDate' );
	gPersianDate();
}

function gPersianDate() {
	return gPersianDateCore::instance();
} 

gpersiandate_init();


/**
require_once( GPERSIANDATE_DIR.'includes/widgets.class.php' );
require_once( GPERSIANDATE_DIR.'includes/settingscore.class.php' );
require_once( GPERSIANDATE_DIR.'includes/plugin.class.php' );
require_once( GPERSIANDATE_DIR.'includes/wiki.class.php' );

defined( 'GPERSIANDATE_TIMEZONE' ) or define( 'GPERSIANDATE_TIMEZONE', gPersianDate::get_timezone() );
defined( 'GPERSIANDATE_LOCALE' ) or define( 'GPERSIANDATE_LOCALE', get_locale() );
defined( 'GPERSIANDATE_FIXNONPERSIAN' ) or define( 'GPERSIANDATE_FIXNONPERSIAN', true );
	
if ( ! function_exists( 'the_dashboard_time' ) ) : function the_dashboard_time( $year = false, $echo = true, $day_week = false ){ return gPersianDate::the_dashboard( $year, $echo, $day_week ); } endif;
if ( ! function_exists( 'the_context_time' ) ) : function the_context_time( $format = 'y/n/j', $echo = true ){ return gPersianDate::the_context( $format, $echo ); } endif;
	
$gPersianDate = gPersianDate::getInstance();	
**/