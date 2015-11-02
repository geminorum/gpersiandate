<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateModuleCore
{

	protected $ajax = FALSE;

	public function __construct()
	{
		if ( ( ! $this->ajax && self::isAJAX() )
			|| ( defined( 'WP_INSTALLING' ) && constant( 'WP_INSTALLING' ) ) )
			return;

		$this->setup_actions();
	}

	protected function setup_actions() {}

	public static function isAJAX()
	{
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}
