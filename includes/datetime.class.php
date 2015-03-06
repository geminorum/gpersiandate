<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateDateTime
{
    public function __construct() 
    {
        if ( ! class_exists( 'ExtDateTime' ) )
			require_once( GPERSIANDATE_DIR.'assets/libs/jDateTime/jdatetime.class.php' );
    }
    
    // http://sallar.me/projects/jdatetime/
    // http://github.com/sallar/jDateTime
	
    
}