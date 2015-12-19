<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCore
{

	private $options = FALSE;

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
		// $this->modules = new stdClass();
	}

	private function setup_actions()
	{
		add_action( 'init', array( $this, 'init' ) );

		do_action_ref_array( 'gpersiandate_after_setup_actions', array( &$this ) );
	}

	private function setup_modules()
	{
		$modules = array(
			'datetime'  => 'gPersianDateDateTime',
			'strings'   => 'gPersianDateStrings',
			'translate' => 'gPersianDateTranslate',
			'timezone'  => 'gPersianDateTimeZone',
			'wordpress' => 'gPersianDateWordPress',
			'adminbar'  => 'gPersianDateAdminBar',
			'date'      => 'gPersianDateDate',
			'format'    => 'gPersianDateFormat',
			'search'    => 'gPersianDateSearch',
			'links'     => 'gPersianDateLinks',
			'admin'     => 'gPersianDateAdmin',
			'archives'  => 'gPersianDateArchives',
			'calendar'  => 'gPersianDateCalendar',
			'plugins'   => 'gPersianDatePlugins',
		);

		foreach ( $modules as $module => $class ) {
			if ( class_exists( $class ) ) {
				try {
					$this->{$module} = new $class;
				} catch ( \Exception $e ) {
					// do nothing!
				}
			}
		}

		add_action( 'bp_include', array( $this, 'bp_include' ) );
		add_action( 'bbp_includes', array( $this, 'bbp_includes' ) );
	}

	public function bp_include()
	{
		if ( class_exists( 'gPersianDateBuddyPress' ) ) {
			try {
				$this->buddypress = new gPersianDateBuddyPress();
			} catch ( \Exception $e ) {
				// do nothing!
			}
		}
	}

	public function bbp_includes()
	{
		if ( class_exists( 'gPersianDateBBPress' ) ) {
			try {
				$this->bbpress = new gPersianDateBBPress();
			} catch ( \Exception $e ) {
				// do nothing!
			}
		}
	}

	public function init()
	{
		load_plugin_textdomain( GPERSIANDATE_TEXTDOMAIN, FALSE, 'gpersiandate/languages' );
	}

	public function options()
	{
		if ( FALSE === $this->options )
			$this->options = get_option( 'gpersiandate', array() );

		return $this->options;
	}
}
