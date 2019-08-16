<?php defined( 'ABSPATH' ) or die( 'Restricted access' );

class gPersianDateCore extends gPersianDateBase
{

	public $base = 'gpersiandate';

	private $options = FALSE;

	private static $instance;

	private function __construct() {}

	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new gPersianDateCore;
			self::$instance->setup();
		}

		return self::$instance;
	}

	private function setup()
	{
		$modules = [
			'datetime'   => 'gPersianDateDateTime',
			'strings'    => 'gPersianDateStrings',
			'translate'  => 'gPersianDateTranslate',
			'timezone'   => 'gPersianDateTimeZone',
			'wordpress'  => 'gPersianDateWordPress',
			'adminbar'   => 'gPersianDateAdminBar',
			'date'       => 'gPersianDateDate',
			'format'     => 'gPersianDateFormat',
			'search'     => 'gPersianDateSearch',
			'shortcodes' => 'gPersianDateShortCodes',
			'archives'   => 'gPersianDateArchives',
			'calendar'   => 'gPersianDateCalendar',
			'plugins'    => 'gPersianDatePlugins',
			'form'       => 'gPersianDateForm',
		];

		if ( is_admin() )
			$modules['admin'] = 'gPersianDateAdmin';
		else
			$modules['links'] = 'gPersianDateLinks';

		foreach ( $modules as $module => $class ) {
			if ( class_exists( $class ) ) {

				try {

					$this->{$module} = new $class;

				} catch ( \Exception $e ) {

					// do nothing!
				}
			}
		}

		add_action( 'bp_include', [ $this, 'bp_include' ] );
		add_action( 'bbp_includes', [ $this, 'bbp_includes' ] );
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 20 );

		// do_action_ref_array( 'gpersiandate_after_setup_actions', [ &$this ] );
	}

	public function plugins_loaded()
	{
		defined( 'GPERSIANDATE_TIMEZONE' ) or define( 'GPERSIANDATE_TIMEZONE', gPersianDateTimeZone::current() );
		defined( 'GPERSIANDATE_LOCALE' ) or define( 'GPERSIANDATE_LOCALE', get_user_locale() );
		defined( 'GPERSIANDATE_FIXNONPERSIAN' ) or define( 'GPERSIANDATE_FIXNONPERSIAN', TRUE );

		load_plugin_textdomain( 'gpersiandate', FALSE, 'gpersiandate/languages' );
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

	public function options()
	{
		if ( FALSE === $this->options )
			$this->options = get_option( 'gpersiandate', [] );

		return $this->options;
	}
}
