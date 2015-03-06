<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCore 
{
    private static $instance;
    
    private function __construct() { /** Do nothing **/ }
    
    public static function instance() 
    {
    	if ( ! isset( self::$instance ) ) {
    		self::$instance = new gPersianDateCore;
    		self::$instance->setup_globals();
    		self::$instance->setup_actions();
    		self::$instance->setup_modules();
    	}
        
    	return self::$instance;
    }

    private function setup_globals() 
    {
    	$this->modules = new stdClass();
    }

    private function setup_actions() 
    {
		add_action( 'init', array( $this, 'init' ) );
        
		do_action_ref_array( 'gpersiandate_after_setup_actions', array( &$this ) );
	}
    
    private function setup_modules()
	{
		$modules = array(
			'datetime' => 'gPersianDateDateTime',
			'date' => 'gPersianDateDate',
			'cal'  => 'gPersianDateCal',
		);
		
		foreach ( $modules as $module => $class )
            if ( class_exists( $class ) )
                $this->{$module} = new $class;	
	}
    
    public function init() 
    {
        load_plugin_textdomain( GPERSIANDATE_TEXTDOMAIN, false, 'gpersiandate/languages' );
    }    
    
}
