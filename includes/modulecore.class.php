<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateModuleCore
{
    
    var $_ajax        = false;      // load if ajax
	var $_dev         = null;       // load if dev
    
    public function __construct()
	{
		if ( ( ! $this->_ajax && self::isAJAX() )
			|| ( defined( 'WP_INSTALLING' ) && constant( 'WP_INSTALLING' ) ) )
			return;

		if ( ! is_null( $this->_dev ) ) {
			if ( false === $this->_dev && self::isDev() )
				return;
			else if ( true === $this->_dev && ! self::isDev() )
				return;
		}

		$this->setup_actions();
	}

    public function setup_actions() {}
    
    public static function isAJAX()
	{
		return ( defined( 'DOING_AJAX' ) && constant( 'DOING_AJAX' ) ) ? true : false;
	}

    public static function isDev()
	{
		if ( defined( 'WP_STAGE' )
			&& 'development' == constant( 'WP_STAGE' ) )
				return true;
		return false;
	}
    
    
}