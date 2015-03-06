<?php defined( 'ABSPATH' ) or die( 'Restricted access' );
class gPersianDate {

	var $_datepicker = false;
	
	public static function getInstance()
	{
		static $instance;
		if ( ! isset( $instance ) )	
			$instance = new gPersianDate();
		return $instance;	
	}

    function __construct()
	{
		$settings_args = array(
			'option_group' => 'gpersiandate',
			'page' => 'writing',
			'sections' => array( 
				'default' => array( 
					'title' => false,
					'callback' => '__return_false',
					'fields' => array(
						'datepicker' => array(
							'title' => __( 'Date Picker', GPERSIANDATE_TEXTDOMAIN ),
							'desc' => __( 'select to enable date picker on manage post screen.', GPERSIANDATE_TEXTDOMAIN ),
							'type' => 'enabled',
							'dir' => 'ltr',
							'default' => 0,
							'filter' => false, // 'esc_attr'
						),
						'admin_bar' => array(
							'title' => __( 'AdminBar DateTime', GPERSIANDATE_TEXTDOMAIN ),
							'desc' => __( 'select to enable current date and time on admin bar.', GPERSIANDATE_TEXTDOMAIN ),
							'type' => 'enabled',
							'dir' => 'ltr',
							'default' => 1,
							'filter' => false, // 'esc_attr'
						),
					),
				),
			),				
		);
		
		$this->settings = new gPersianDateSettingsCore( array(), $settings_args );
		$this->_datepicker = $this->settings->get_option( 'datepicker', false );	
		$this->_adminbar = $this->settings->get_option( 'admin_bar', true );	
	
		add_action( 'plugins_loaded', array( & $this, 'plugins_loaded' ) );
		add_action( 'bp_include', array( & $this, 'bp_include' ) ); // BuddyPress
		add_action( 'bbp_includes', array( & $this, 'bbp_includes' ) ); // BBPress
	
		add_action( 'init', array( & $this, 'init' ) );
		//add_action( 'admin_init', array( & $this, 'admin_init' ) );
		add_action( 'widgets_init', array( & $this, 'widgets_init' ), 20 );
		//add_action( 'wp_footer', array( & $this, 'wp_footer' ) );
		
		add_filter( 'posts_request', array( & $this, 'posts_request' ), 20 );
		add_filter( 'posts_search', array( & $this, 'posts_request' ), 20 );
		add_filter( 'posts_where', array( & $this, 'posts_where' ), 20 );
		add_filter( 'get_search_query', array( $this, 'translate_chars' ) );
		
		add_filter( 'post_link', array( & $this, 'post_link' ), 10, 3 );
		add_filter( 'day_link', array( & $this, 'day_link' ), 10, 4 );
		add_filter( 'month_link', array( & $this, 'month_link' ), 10, 3 );
		add_filter( 'year_link', array( & $this, 'year_link' ), 10, 2 );
		
		add_filter( 'wp_title_parts', array( & $this, 'wp_title_parts' ) );
		
		add_filter( 'the_title', array( $this, 'replace_numbers' ), 12 );
		add_filter( 'the_content', array( $this, 'replace_numbers' ), 12 );
		add_filter( 'get_the_excerpt', array( $this, 'translate_numbers_html' ), 12 );
		add_filter( 'get_comment_excerpt', array( $this, 'translate_numbers_html' ), 12 );
		add_filter( 'get_comment_text', array( $this, 'translate_numbers_html' ), 12 );
		
		// NOT WORKING!!!! (probably because of next en chars!)
		//add_filter( 'wp_list_categories', array( $this, 'replace_numbers' ), 12, 2 );
		//add_filter( 'wp_dropdown_cats', array( $this, 'replace_numbers' ), 12 );
		
		add_filter( 'update_footer', array( $this, 'translate_numbers_html' ), 12 );
		
		add_filter( 'pre_insert_term', array( & $this, 'pre_insert_term' ), 10, 2 );
		add_filter( 'pre_term_name', array( $this, 'translate_numbers' ) );
		add_filter( 'pre_term_description', array( $this, 'translate_numbers_html' ) );
		
		add_filter( 'date_i18n', array( & $this, 'date_i18n' ), 10, 4 );
		add_filter( 'number_format_i18n', array( & $this, 'format_i18n' ), 10, 2 );
		add_filter( 'bb_number_format_i18n', array( & $this, 'format_i18n' ), 10, 1 );
		
		// our filters!
		add_filter( 'number_format_i18n_back', array( $this, 'format_i18n_back' ) );
		add_filter( 'string_format_i18n', array( & $this, 'format_i18n' ) );
		add_filter( 'html_format_i18n', array( $this, 'translate_numbers_html' ) );
		
		add_filter( 'maybe_format_i18n', array( $this, 'replace_numbers' ), 10, 2 );
		
		add_filter( 'gmeta_meta', array( $this, 'replace_numbers' ), 12 );
		add_filter( 'gmeta_lead', array( $this, 'replace_numbers' ), 12 );
		add_filter( 'geditorial_kses', array( $this, 'replace_numbers' ), 12 );
		
		add_filter( 'wp_nav_menu_items', array( & $this, 'wp_nav_menu_items' ), 10, 2 );
		
		//add_filter( 'the_date', array( & $this, 'the_date' ), 10, 4 );
		add_filter( 'get_the_date', array( & $this, 'get_the_date' ), 10, 2 ); // must translate
		
		//add_filter( 'the_time', array( & $this, 'the_time' ), 10, 1 ); // the function has a translate option
		//add_filter( 'get_the_time', array( & $this, 'get_the_time' ), 10, 3 ); // the function has a translate option
		//add_filter( 'get_post_time', array( & $this, 'get_post_time' ), 10, 3 ); // the function has a translate option
		//add_filter( 'get_post_modified_time', array( & $this, 'get_post_modified_time' ), 10, 3 ); // the function has a translate option
		
		//add_filter( 'get_comment_time', array( & $this, 'get_comment_time' ), 10, 4 ); // the function has a translate option
		add_filter( 'get_comment_date', array( & $this, 'get_comment_date' ), 10, 2 );  // must translate
		
		// gShop
		add_filter( 'gshop_stats_current_month', array( & $this, 'gshop_stats_current_month' ), 10, 3 );
		
		if ( is_admin() ) {
			// ? : must be admin?
			add_filter( 'gettext', array( & $this, 'gettext' ), 10, 3 );
			//add_filter( 'gettext_with_context', array( & $this, 'gettext_with_context' ), 10, 4 ); // no need for now
		
			add_filter( 'date_formats', array( & $this, 'date_formats' ) );
			add_filter( 'time_formats', array( & $this, 'time_formats' ) );
			add_filter( 'pre_option_start_of_week', array( & $this, 'pre_option_start_of_week' ) );
			add_filter( 'default_option_start_of_week', array( & $this, 'pre_option_start_of_week' ) );
			
			add_filter( 'pre_get_posts', array( & $this, 'pre_get_posts' ) );
			add_action( 'restrict_manage_posts', array( & $this, 'restrict_manage_posts_mgp' ), 5 );
			
			add_action( 'admin_enqueue_scripts', array( & $this, 'admin_enqueue_scripts' ) );
			
			//add_action( 'post_submitbox_misc_actions', array( & $this, 'post_submitbox_misc_actions' ) );
		} else {
			// some filters fo non admin area. just to be neat!
			add_filter( 'list_pages', array( $this, 'replace_numbers' ), 12 ); // page dropdown walker item title
			
			add_filter( 'wp_enqueue_scripts', array( & $this, 'wp_enqueue_scripts' ) );
		}
		
	}
	
	public function plugins_loaded()
	{
		if ( ! class_exists( 'ExtDateTime' ) )
			require_once( GPERSIANDATE_DIR.'ExtDateTime/ExtDateTime.php' );
	}
	
	public function init() 
	{ 
		load_plugin_textdomain( GPERSIANDATE_TEXTDOMAIN, false, 'gpersiandate/languages' ); 
		
		if ( is_admin_bar_showing() && $this->_adminbar && is_user_logged_in() ) {
		
			add_action( 'admin_bar_menu', array( & $this, 'admin_bar_menu' ) );
			
			// http://www.jquery4u.com/snippets/create-jquery-digital-clock-jquery4u/
			wp_register_script( 'gperdiandate-clock',
				GPERSIANDATE_URL.'assets/js/adminbar.clock.min.js',
				array( 'jquery' ),
				GPERSIANDATE_VERSION,
				true
			);
			
			wp_localize_script( 'gperdiandate-clock',
				'GPD_clock', array( 
					'local' => GPERSIANDATE_LOCALE,
			) );
			
		} else {
		
			$this->_adminbar = false;
		
		}		
	}
	
	public function admin_enqueue_scripts()
	{
		$screen = get_current_screen();
		
		if ( $this->_datepicker && 'edit' == $screen->base ) {
			
			wp_deregister_script( 'jquery-ui-datepicker' );
			wp_register_script( 'jquery-ui-datepicker', GPERSIANDATE_URL.'assets/libs/datepicker/scripts/jquery.ui.datepicker-cc.all.min.js', array( 'jquery', 'jquery-ui-core' ), GPERSIANDATE_VERSION );
			
			wp_enqueue_script( 'gpersiandate-editdate', GPERSIANDATE_URL.'assets/js/edit.date.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), GPERSIANDATE_VERSION, true );
			wp_localize_script( 'gpersiandate-editdate', 
				'GPD_Edit', array(
					'fromButtonImage' => GPERSIANDATE_URL.'assets/images/fugue/calendar-select.png',
					'toButtonImage' => GPERSIANDATE_URL.'assets/images/fugue/calendar-select-days.png',
					'fromButtonText' => _x( 'From', 'Date picker Image Title', GPERSIANDATE_TEXTDOMAIN ),
					'toButtonText' => _x( 'To', 'Date picker Image Title', GPERSIANDATE_TEXTDOMAIN ),
			) );
			
			add_action( 'admin_print_styles', array( & $this, 'admin_print_styles' ) );
			add_action( 'restrict_manage_posts', array( & $this, 'restrict_manage_posts_start_end' ) );
		}
		
		if ( $this->_adminbar )
			wp_enqueue_script( 'gperdiandate-clock' );
		
	}
	
	public function admin_print_styles()
	{
		echo '<link rel="stylesheet" href="'.GPERSIANDATE_URL.'assets/libs/datepicker/styles/jquery-ui-modified.css" type="text/css" />';
		echo '<link rel="stylesheet" href="'.GPERSIANDATE_URL.'assets/css/edit.css" type="text/css" />';
	}
	
	public function wp_enqueue_scripts() 
	{
		if ( $this->_adminbar )			
            wp_enqueue_script( 'gperdiandate-clock' );
	}
	
	public function bp_include()
	{
		remove_filter( 'bp_get_total_group_count_for_user', 'bp_core_number_format' );
		remove_filter( 'bp_get_total_mention_count_for_user', 'bp_core_number_format' );
		remove_filter( 'bp_get_total_favorite_count_for_user', 'bp_core_number_format' );

		add_filter( 'bp_core_time_since', array( $this, 'translate_numbers' ) );
	}
	
	public function bbp_includes()
	{
		add_filter( 'bbp_number_format', array( $this, 'translate_numbers' ), 12 );
		add_filter( 'bbp_get_time_since', array( $this, 'translate_numbers' ), 12 );
	}
	
	function widgets_init()
	{
		global $wp_widget_factory;
		$wp_widget_factory->widgets['WP_Widget_Archives'] = new WP_Widget_Persian_Archives();
	}	
	
	function date_i18n( $j, $req_format, $i, $gmt )
	{
		$date = ExtDateTime::factory( 'Persian', $i, 'UTC', array( & $this, 'translator' ) );
		return self::translate_numbers( $date->format( self::translate_format( $req_format ) ) );
	} 
	
	public static function date( $format, $time = null, $time_zone = GPERSIANDATE_TIMEZONE, $local = GPERSIANDATE_LOCALE, $translate_numbers = true ) 
	{
		if ( self::check_iso( $format ) ) 
			return mysql2date( $format, ( is_null( $time ) ? current_time( 'timestamp' ) : $time ), false ); 
		
		$date = ExtDateTime::factory( 'Persian', $time, 'UTC', array( __CLASS__, 'translator' ) );
		$date_string = $date->format( $format );
		return ( $translate_numbers ? self::translate_numbers( $date_string, $local ) : $date_string );
	}
	
	//for full ISO time
	public static function check_iso( $format ) 
	{
		if ( in_array( $format, array( 'U', 'r', 'c', 'G', 'Y-m-d_H-i-s', 'Y-m-d H:i:s' ) ) ) // WTF: wierd behavior for: 'Y-m-d\TH:i:s\Z' 
			return true;
		return false;
	}

	// MAYBE: a problem since the filter does not pass $translate
	function get_post_time( $time, $d, $gmt )
	{
		if ( is_feed() )
			return $time;
	
		if ( self::check_iso( $d ) ) 
			return $time;
	
		// TODO: see this : http://stackoverflow.com/a/7536768/642752
	
		// if it's already converted!
		if ( ! strtotime( $time ) ) 
			return $time;
		
		// MAYBE: a problem since the filter does not pass the $post
		// POSSIBLE SOLUTION: check the filter input with current global post!
		$the_post = get_post( null );
		return mysql2date( $d, ( $gmt ? $the_post->post_date_gmt : $the_post->post_date ), true );
	}
	
	// MAYBE: a problem since the filter does not pass $translate	
	function get_post_modified_time( $time, $d, $gmt )
	{
		if ( is_feed() )
			return $time;
	
		if ( self::check_iso( $d ) ) 
			return $time;
	
		// if it's already converted!
		if ( ! strtotime( $time ) ) 
			return $time;
		
		// MAYBE: a problem since the filter does not pass the $post
		// POSSIBLE SOLUTION: check the filter input with current global post!
		$the_post = get_post( null );
		return mysql2date( $d, ( $gmt ? $the_post->post_modified_gmt : $the_post->post_modified ), true );	
	}
	
	
	public static function post_time( $gmt = false, $post = null )
	{
		$the_post = get_post( $post );
		return mysql2date( 'U', ( $gmt ? $the_post->post_date_gmt : $the_post->post_date ), false );
	}
	
	function comment_time( $gmt = false )
	{
		global $comment;
		return mysql2date( 'U', ( $gmt ? $comment->comment_date_gmt : $comment->comment_date ), false );
	}
	
	function format( $format = '', $context = 'date' ) 
	{
		if ( '' == $format )
			//return $this->_constants['format'][$context];
			return get_option( $context.'_format' );
		return $format;
	}
	
	// MUST DEP : NO NEED! MAYBE!
	function the_date( $the_date, $d = '', $before = '', $after = '' ) 
	{ 
		return $before.self::date( self::format( $d, 'date' ), self::post_time() ).$after; 
	}
	
	function get_the_date( $given = '', $d = '' ) 
	{ 
		return self::date( self::format( $d, 'date' ), self::post_time() ); 
	}
	
	// MUST DEP : NO NEED! MAYBE!
	function the_time( $d = '' ) 
	{ 
		return self::date( self::format( $d, 'time' ), self::post_time() ); 
	}
	
	// MUST DEP : NO NEED! MAYBE!
	function get_the_time( $the_time = '', $d = '', $post = null )
	{ 
		return self::date( self::format( $d, 'time' ), self::post_time( false, $post ) );
	}
	
	function get_comment_date( $date = '', $d = '' ) 
	{ 
		return self::date( self::format( $d, 'date' ), self::comment_time() );
	}	

	function get_comment_time( $date = '', $d = '', $gmt = false, $translate = true )
	{
		if ( $translate )
			return self::date( self::format( $d, 'time' ), self::comment_time( $gmt ) ); 
		return $date;
	}

	// RECHECK!
	public static function the_dashboard( $year = false, $echo = true, $day_week = false )
	{
		//gtheme_dump( date ( 'D, d M Y H:i:s P', self::post_time() )); die();
		//gtheme_dump( self::post_time()); die();
		//gtheme_dump( self::post_time()); //die();
		//gtheme_dump( date_i18n( 'D, d M Y H:i:s', strtotime( self::post_time() ) ) ); //die();
		
		$date = ExtDateTime::factory( 'Persian', 
			self::post_time(),
			constant( 'GPERSIANDATE_TIMEZONE' ),
			array( __CLASS__, 'translator' ) 
		);
		
		//gtheme_dump( $date->format( 'D, d M Y H:i:s P' ) ); die();
		
		$result = '';
		if ( $day_week )
			$result .= $date->format( 'l' ).' ';
		
		$result .= self::translator( $date->format( 'j' ) ).' '; // TODO : it uses the __(), but there must be a safer way.
		
		if ( $year )
			$result .= self::translate_numbers( $date->format( 'F Y' ) );
		else
			$result .= $date->format( 'F' ); 
		
		if ( $echo ) 
			echo esc_html( $result ); 
		else 
			return $result;
	}

	
	// RECHECK!
	public static function the_context( $format = 'y/n/j', $echo = true )
	{
		global $post;
		$time = get_post_time( 'G', true, $post, false );
		$time_diff = time() - $time;
		
		//echo sprintf( __( '%s ago' ), self::translate_numbers( human_time_diff( $time ) ) ); return;
		
		if ( $time_diff > 0 && $time_diff < 72*60*60 ) 
			$h_time = sprintf( __( '%s ago' ), self::translate_numbers( human_time_diff( $time ) ) );
			//$h_time = sprintf( __( '%s ago' ), self::translate_numbers( human_time_diff( $time ) ) );
		else 
			$h_time = get_the_time( $format );
		
		if ( $echo ) 
			echo esc_html( $h_time ); 
		else 
			return $h_time;
	}


	// http://wpquicktips.wordpress.com/2012/12/19/human-readable-date/
	// add_filter( 'the_date', 'mytheme_the_date' );
	// TODO : adapt
	function mytheme_the_date( $date ) 
	{
		$time = strtotime( $date );
		$difference = time() - $time;
		$days_ago = (int)( $difference / 60 / 60 / 24 );

		if ( date( 'Y-m-d' ) === date( 'Y-m-d', $time ) )
		   return __( 'Today', GPERSIANDATE_TEXTDOMAIN );
		elseif ( date( 'Y-m-d', strtotime('-1 day') ) === date( 'Y-m-d', $time ) )
		   return __( 'Yesterday', GPERSIANDATE_TEXTDOMAIN );
		elseif ( $days_ago < 7 )
		   return sprintf( __( '%d days ago', GPERSIANDATE_TEXTDOMAIN ), $days_ago );

		return $date;
	}

	// Originally from wp-jalali
	public static function posts_request( $query ) 
	{
		if ( strstr( $query, 'LIKE' ) ) 
			if ( strstr( $query, "ی" ) 
				|| strstr( $query, "ک" ) 
				|| strstr( $query, "ي" ) 
				|| strstr( $query, "ك" ) 
				|| strstr( $query, "٤" ) 
				|| strstr( $query, "٥" ) 
				|| strstr( $query, "٦") 
				|| strstr( $query, "۴" ) 
				|| strstr( $query, "۵" ) 
				|| strstr( $query, "۶" ) )
					$query = preg_replace_callback( "/(\([^\)\(]* LIKE '([^']*)'\))/", 
						array( __CLASS__, 'posts_request_callback' ), $query );
		return $query;
	} 

	// Originally from wp-jalali
	public static function posts_request_callback( $matches ) 
	{
		return 
			"( ".
			str_replace( 
				$matches[2], 
				str_replace( 
					array( "ي", "ك", "٤", "٥", "٦" ),
					array( "ی", "ک", "۴", "۵", "۶" ), 
					$matches[2] 
				), 
				$matches[1] 
			)." OR ".
			str_replace( 
				$matches[2], 
				str_replace( 
					array( "ی", "ک", "۴", "۵", "۶" ), 
					array( "ي", "ك", "٤", "٥", "٦" ), 
					$matches[2] 
				), $matches[1] 
			)." )";
	}

	// USE : defined( 'GPERSIANDATE_SKIP' ) or define( 'GPERSIANDATE_SKIP', true );
	// Originally from farhadi.ir & wp-jalali
	public static function replace_numbers( $content, $first = null, $second = null ) 
	{
		if ( defined( 'GPERSIANDATE_SKIP' ) && GPERSIANDATE_SKIP ) return $content;
		
		//$pattern = '/(?:&#\d{2,4};)|((?:\&nbsp\;)*\d+(?:\&nbsp\;)*\d*\.*(?:\&nbsp\;)*\d*(?:\&nbsp\;)*\d*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i';
		$pattern = '/(?:&#\d{2,4};)|(\d+[\.\d]*)|(?:[a-z](?:[\x00-\x3B\x3D-\x7F]|<\s*[^>]+>)*)|<\s*[^>]+>/i';
		
		return self::translate_chars( preg_replace_callback( $pattern, array( __CLASS__, 'translate_numbers_callback' ), $content ) );
	}
	
	public static function translate_numbers_callback( $matches ) 
	{
		if ( isset( $matches[1] ) ) 
			return self::translate_numbers( $matches[1] ); 
		else 
			return $matches[0];
	}

	public static function translator( $string ) 
	{ 
		return __( $string, GPERSIANDATE_TEXTDOMAIN ); 
	}
	
	public static function translate_format( $format = '' )
	{
		// TODO : apply filters
		
		if ( 'M j, Y @ G:i' == $format && 'fa_IR' == constant( 'GPERSIANDATE_LOCALE' ) )
			return 'j M Y @ G:i';
			
			// 'Y/m/d g:i:s A'
			
		return $format;
	}

	public static function translate_numbers( $text, $local = GPERSIANDATE_LOCALE )
	{
		if ( is_null( $text ) ) 
			return null;

		switch( $local ) {
			//case 'en_US' : 
			case 'fa_IR' : { 
				$text = strtr( $text, array(	
					'0' => chr(0xDB).chr(0xB0),
					'1' => chr(0xDB).chr(0xB1),
					'2' => chr(0xDB).chr(0xB2),
					'3' => chr(0xDB).chr(0xB3),
					'4' => chr(0xDB).chr(0xB4),
					'5' => chr(0xDB).chr(0xB5),
					'6' => chr(0xDB).chr(0xB6),
					'7' => chr(0xDB).chr(0xB7),
					'8' => chr(0xDB).chr(0xB8),
					'9' => chr(0xDB).chr(0xB9) ) );
				break;
			}
		}
		return self::translate_chars( $text );
	}
		
	// http://www.ltg.ed.ac.uk/~richard/utf-8.cgi
	public static function translate_chars( $text, $fix = GPERSIANDATE_FIXNONPERSIAN )
	{
		if ( $fix ) {
			return strtr( $text, array(	
				chr(0xD9).chr(0xA0) => chr(0xDB).chr(0xB0),
				chr(0xD9).chr(0xA1) => chr(0xDB).chr(0xB1),
				chr(0xD9).chr(0xA2) => chr(0xDB).chr(0xB2),
				chr(0xD9).chr(0xA3) => chr(0xDB).chr(0xB3),
				chr(0xD9).chr(0xA4) => chr(0xDB).chr(0xB4),
				chr(0xD9).chr(0xA5) => chr(0xDB).chr(0xB5),
				chr(0xD9).chr(0xA6) => chr(0xDB).chr(0xB6),
				chr(0xD9).chr(0xA7) => chr(0xDB).chr(0xB7),
				chr(0xD9).chr(0xA8) => chr(0xDB).chr(0xB8),
				chr(0xD9).chr(0xA9) => chr(0xDB).chr(0xB9),
				chr(0xD9).chr(0x83) => chr(0xDA).chr(0xA9), // ARABIC LETTER KAF > ARABIC LETTER KEHEH
				chr(0xD9).chr(0x89) => chr(0xDB).chr(0x8C), // ARABIC LETTER ALEF MAKSURA > ARABIC LETTER FARSI YEH
				chr(0xD9).chr(0x8A) => chr(0xDB).chr(0x8C), // ARABIC LETTER YEH > ARABIC LETTER FARSI YEH
				chr(0xDB).chr(0x80) => chr(0xD9).chr(0x87) . chr(0xD9).chr(0x94) ) ) ;
				
				// http://stackoverflow.com/a/13481824
				// chr(0xE2).chr(0x80).chr(0x8C), // ZERO WIDTH NON-JOINER (U+200C) : &zwnj; 
				
		}
		return $text;
	}
		
	public static function translate_numbers_back( $text, $local = GPERSIANDATE_LOCALE )
	{
		if ( is_null( $text ) ) 
			return null;

		switch( $local ) {
			//case 'en_US' : 
			case 'fa_IR' : { 
				$text = strtr( $text, array(	
					chr(0xDB).chr(0xB0) => '0',
					chr(0xDB).chr(0xB1) => '1',
					chr(0xDB).chr(0xB2) => '2',
					chr(0xDB).chr(0xB3) => '3',
					chr(0xDB).chr(0xB4) => '4',
					chr(0xDB).chr(0xB5) => '5',
					chr(0xDB).chr(0xB6) => '6',
					chr(0xDB).chr(0xB7) => '7',
					chr(0xDB).chr(0xB8) => '8',
					chr(0xDB).chr(0xB9) => '9',
				) );
				break;
			}
		}
		
		// todo : strip non numerial
		return intval( $text );
	}	

	
	// alias for replace_numbers
	public static function translate_numbers_html( $html )
	{
		return self::replace_numbers( $html );
	}
	
	function format_i18n( $formatted, $decimals = 0 ) 
	{ 
		return self::translate_numbers( $formatted );
	} 

	public static function format_i18n_back( $formatted, $local = GPERSIANDATE_LOCALE  ) 
	{
		return self::translate_numbers_back( $formatted, $local );
	} 
	
	function gettext_with_context( $translations, $text, $context, $domain ) 
	{
		return $this->gettext( $translations, $text, $domain );
	}
	
	function gettext( $translations, $text, $domain )
	{ 
		if ( 'default' != $domain )
			return $translations;

		// TODO : add filters
		$strings = array(
			
			// on touch_time()
			/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
			'%1$s %2$s, %3$s @ %4$s : %5$s' => ( 'fa_IR' == GPERSIANDATE_LOCALE ? '%2$s%1$s%3$s @ %5$s:%4$s' : '%2$s%1$s%3$s @ %4$s:%5$s' ),
			
			/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
			'Y-m-d G:i:s' => 'G:i:s Y-m-d',
			
			// ADMIN DAHSBOARD ACTIVITY WIDGET
			/* translators: date and time format for recent posts on the dashboard, see http://php.net/date */
			'M jS' => 'j M Y',
			/* translators: 1: relative date, 2: time, 3: post edit link, 4: post title */
			'<span>%1$s, %2$s</span> <a href="%3$s">%4$s</a>' => '<span>%1$s &ndash; %2$s</span> <a href="%3$s">%4$s</a>',
			
			'Howdy, %1$s' => '%1$s',

		);
		
		if ( isset( $strings[$text] ) )
			return $strings[$text];
 
		return $translations;
	}

	// Menu Navigation Date handler
	// just put {TODAY_DATE} on a menu item text!
	// TODO: disable option, format option
	function wp_nav_menu_items( $items, $args ) 
	{
		return preg_replace( '%{TODAY_DATE}%', date_i18n( 'j M Y' ), $items );
	} 

	function admin_bar_menu( $wp_admin_bar )
	{
		if ( ! is_user_logged_in() )
			return;
			
		//$title = '<span '.( is_rtl() ? 'style="direction:rtl !important;" ' : '' ).'id="gpd-today">'.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span> - <span id="gpd-now">'.date_i18n( 'H:i' ).'</span>';
		
		if ( is_rtl() )
			$title = '<span id="gpd-now">'.date_i18n( 'H:i' ).'</span> - <span id="gpd-today">'.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span>';
		else 
			$title = '<span id="gpd-today">'.date_i18n( get_option( 'date_format', 'j M Y' ) ).'</span> - <span id="gpd-now">'.date_i18n( 'H:i' ).'</span>';	
			
		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate',
			'title'  => $title,
			'parent' => 'top-secondary', // Off on the right side
			'href'   => ( current_user_can( 'manage_options' ) ? get_admin_url( null, 'options-general.php' ) : false ),
		) );
		
		return;
		
		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate-today',
			'title'  => esc_html( date_i18n( get_option( 'date_format', 'j M Y' ) ) ),
			'parent' => 'top-secondary',
			'href'   => false,
			'meta'   => array(
				'title'  => ( is_admin() ? esc_html__( 'Today in Persian ( just to make sure the conversion is intact )', GPERSIANDATE_TEXTDOMAIN ) : esc_html__( 'Today in Persian', GPERSIANDATE_TEXTDOMAIN ) ),
			),
		) );

		$wp_admin_bar->add_node( array(
			'id'     => 'gpersiandate-now',
			'title'  => esc_html( date_i18n( 'H:i' ) ), // get_option( 'time_format', 'g:i A' )
			'parent' => 'top-secondary',
			'href'   => false,
			'meta'   => array(
				'title'  => ( is_admin() ? esc_html__( 'Now ( just to make sure time zone is correct )', GPERSIANDATE_TEXTDOMAIN ) : esc_html__( 'Just Now', GPERSIANDATE_TEXTDOMAIN ) ),
			),
		) );
	} 

	/** FORMATS : http://codex.wordpress.org/Formatting_Date_and_Time **/
	function date_formats( $formats )
	{
		// TODO : what about local?
		return array(
			'j F Y',
			'y/n/d',
			'y/m/d',
			'Y/n/d',
			'Y/m/d',
			//'l S F Y', // TODO : must support "l" : (st, nd or th in the 1st, 2nd or 15th.)
			__( 'F j, Y' ),
		);
	} 
	

	function time_formats( $formats ) 
	{
		return array(
			'H:i',
			//'g:i A',
			__('g:i a'),
		);
	} 

	function pre_option_start_of_week( $value )
	{
		return 6;
	} 
	
	function restrict_manage_posts_start_end() 
	{
		//TODO : set maximum and minimum date based on stored posts
		
		$start_date = isset( $_REQUEST['start_date_gp'] ) ? $_REQUEST['start_date_gp'] : '';
		$end_date = isset( $_REQUEST['end_date_gp'] ) ? $_REQUEST['end_date_gp'] : '';

		?><span class="gpersiandate-datepicker"><input type="text" name="start_date_gp" id="start_date_gp" class="datepick" value="<?php echo $start_date;?>"
		placeholder="<?php echo esc_attr( __( 'From', GPERSIANDATE_TEXTDOMAIN ) ); ?>" /></span> <?php 

		?><span class="gpersiandate-datepicker"><input type="text" name="end_date_gp" id="end_date_gp" class="datepick" value="<?php echo $end_date;?>"
		placeholder="<?php echo esc_attr( __( 'To', GPERSIANDATE_TEXTDOMAIN ) ); ?>" /></span> <?php 
	
	}
	
	function restrict_manage_posts_mgp() 
	{
		global $post_type, $wpdb;

		$query = $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month, DAY( post_date ) as day
			FROM $wpdb->posts
			WHERE post_type = %s AND post_status <> 'auto-draft'
			ORDER BY post_date DESC
			", $post_type );
		
		$key = md5( $query );
		$cache = wp_cache_get( 'wp_get_archives' , 'general' );
		
		if ( ! isset( $cache[ $key ] ) ) {
			$months = $wpdb->get_results( $query );
			$cache[ $key ] = $months;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$months = $cache[ $key ];
		}
		
		add_action( 'admin_footer', array( & $this, 'admin_footer_mgp' ) );
		
		$month_count = count( $months );
		if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;
			
		$mgp = isset( $_GET['mgp'] ) ? (int) $_GET['mgp'] : 0;
		$last_persian_month = false;
		
		?><select name="mgp" id="gpersiandate-mgp">
			<option <?php selected( $mgp, 0 ); ?> value="0"><?php _e( 'Show all dates', GPERSIANDATE_TEXTDOMAIN ); ?></option><?php
			foreach ( $months as $arc_row ) {
				if ( 0 == $arc_row->year )
					continue;

				$the_date = mktime( 0 ,0 , 0, zeroise( $arc_row->month, 2 ), $arc_row->day, $arc_row->year );
				$the_persian_month = self::date( 'Ym', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
				
				if ( $last_persian_month != $the_persian_month ) {
					printf( '<option %s value="%s">%s</option>'."\n",
						selected( $mgp, $the_persian_month, false ),
						esc_attr( $the_persian_month ),
						self::date( 'M Y', $the_date )
					);
				}
				$last_persian_month = $the_persian_month;
			}
		?></select> <?php
	}
	
	function admin_footer_mgp() 
	{
		?><script type="text/javascript" language="javascript">
		jQuery(document).ready(function(){jQuery('select[name="m"]').hide()});
		</script><?php
	}

	function pre_get_posts( $query )
	{
		global $pagenow;
		
		if( $query->is_admin && ( 'edit.php' == $pagenow ) ) { 
			if( isset( $_REQUEST['start_date_gp'] ) || isset( $_REQUEST['end_date_gp'] ) )
				add_filter( 'posts_where', array( & $this, 'posts_where_start_end' ) );

			if( isset( $_REQUEST['mgp'] ) && 0 != $_REQUEST['mgp'] )
				add_filter( 'posts_where', array( & $this, 'posts_where_mgp' ) );
		}
		
		return $query;
	}
	
	function posts_where_mgp( $where = '' )
	{
		if( isset( $_REQUEST['mgp'] ) && ! empty( $_REQUEST['mgp'] ) ) {
			$days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
			$mgp = ''.preg_replace( '|[^0-9]|', '', $_REQUEST['mgp'] );
			$year = substr( $mgp, 0, 4 );
			$month = substr( $mgp, 4, 2 );
			//$first_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $year.'/'.$month.'/'.'01' ) );
			//$last_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $year.'/'.$month.'/'.gPersianDateHelper::j_last_day_of_month( $month ) ) );

			$first_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $month, 1, $year ) );
			$last_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $month, $days_in_month[$month-1], $year ) );
			
			$where .= " AND post_date >='$first_day' AND post_date <='$last_day' ";
		}
		return $where;	
	}
	
	function posts_where_start_end( $where = '' )
	{
		global $wpdb;
		
		if( isset( $_REQUEST['start_date_gp'] ) && ! empty( $_REQUEST['start_date_gp'] ) ) {
			//$start_date = date( 'Y-m-d', gPersianDateHelper::convert_back( $_REQUEST['start_date_gp'] ) );
			$start = explode( '/', $_REQUEST['start_date_gp'] );
			$start_date = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $start[1], $start[2], $start[0] ) );
			$where .= " AND post_date >='$start_date' ";
		}
		
		if( isset( $_REQUEST['end_date_gp'] ) && ! empty( $_REQUEST['end_date_gp'] ) ) {
			//$end_date = date( 'Y-m-d', gPersianDateHelper::convert_back( $_REQUEST['end_date_gp'] ) );
			$end = explode( '/', $_REQUEST['end_date_gp'] );
			$end_date = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $end[1], $end[2], $end[0] ) );
			$where .= " AND post_date <='$end_date' ";
		}
		
		return $where;	
	}
	
	function post_submitbox_misc_actions()
	{
		return;
	
		global $action, $post;

		$post_type = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );
	
		if ( ! $can_publish )
			return;
			
		?><div class="misc-pub-section curtime curtime-gp">
			<a href="#edit_timestamp" class="1edit-timestamp edit-timestamp-gp hide-if-no-js"><?php _e( 'Edit Persian', GPERSIANDATE_TEXTDOMAIN ); ?></a>
			<div id="timestampdiv-gp" class="hide-if-js"><?php touch_time( ( $action == 'edit' ), 1 ); ?></div>
		</div><?php 

	}
	
	public static function get_timezone() 
	{
		$timezone = get_option( 'timezone_string' ); //default is 'UTC' // 'Asia/Tehran'
		if ( ! empty( $timezone ) )
			return $timezone;
		return self::get_timezone_from_offset( get_option( 'gmt_offset', '0' ) );
	}
	
	public static function get_timezone_from_offset( $offset )
	{
	    $timezones = array( 
			'-12' => 'Pacific/Kwajalein', 
			'-11' => 'Pacific/Samoa', 
			'-10' => 'Pacific/Honolulu', 
			'-9' => 'America/Juneau', 
			'-8' => 'America/Los_Angeles', 
			'-7' => 'America/Denver', 
			'-6' => 'America/Mexico_City', 
			'-5' => 'America/New_York', 
			'-4' => 'America/Caracas', 
			'-3.5' => 'America/St_Johns', 
			'-3' => 'America/Argentina/Buenos_Aires', 
			'-2' => 'Atlantic/Azores',// no cities here so just picking an hour ahead 
			'-1' => 'Atlantic/Azores', 
			'0' => 'Europe/London', 
			'1' => 'Europe/Paris', 
			'2' => 'Europe/Helsinki', 
			'3' => 'Europe/Moscow', 
			'3.5' => 'Asia/Tehran', 
			'4' => 'Asia/Baku', 
			'4.5' => 'Asia/Kabul', 
			'5' => 'Asia/Karachi', 
			'5.5' => 'Asia/Calcutta', 
			'6' => 'Asia/Colombo', 
			'7' => 'Asia/Bangkok', 
			'8' => 'Asia/Singapore', 
			'9' => 'Asia/Tokyo', 
			'9.5' => 'Australia/Darwin', 
			'10' => 'Pacific/Guam', 
			'11' => 'Asia/Magadan', 
			'12' => 'Asia/Kamchatka' 
		);
		
		if ( isset( $timezones[$offset] ) )
			return $timezones[$offset];
		return '0';
	}

	// Originally from : http://wordpress.org/plugins/easy-digital-downloads/
	// using like : date_default_timezone_set( get_timezone_id() );
	function get_timezone_id() 
	{
		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) )
			return $timezone;

		// get UTC offset, if it isn't set return UTC
		if ( ! ( $utc_offset = 3600 * get_option( 'gmt_offset', 0 ) ) )
			return 'UTC';

		// attempt to guess the timezone string from the UTC offset
		$timezone = timezone_name_from_abbr( '', $utc_offset );

		// last try, guess timezone string manually
		if ( $timezone === false ) {

			$is_dst = date('I');

			foreach ( timezone_abbreviations_list() as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['dst'] == $is_dst &&  $city['offset'] == $utc_offset )
						return $city['timezone_id'];
				}
			}
		}

		// fallback
		return 'UTC';
	}
	
	public static function mktime( $hour, $minute, $second, $jmonth, $jday, $jyear ) 
	{
		$date = ExtDateTime::factory( 'Persian' );
		list( $year, $month, $day ) = $date->jalaliToGregorian( $jyear, $jmonth, $jday );
		return mktime( (int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year );
	}
	
	// Originally from wp-jalali
	function posts_where( $where = '' )
	{
		global $wpdb, $wp_query;
		if ( is_admin() || ! $wp_query->is_main_query() )
			return $where;
			
		$conversion = false;		
		$days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$start = $end = array(
			'year' => 1,
			'monthnum' => 1,
			'day' => 1,
			'hour' => 0,
			'minute' => 0,
			'second' => 0,
		);
		
		if ( isset( $wp_query->query_vars['m'] ) 
			&& ! empty( $wp_query->query_vars['m'] ) ) {
			
			$m = ''.preg_replace( '|[^0-9]|', '', $wp_query->query_vars['m'] );
			$start['year'] = substr( $m, 0, 4 );
			
			if ( $start['year'] < 1700 ) {
			
				$conversion = true;
				$end['year'] = $start['year'] + 1;

				if ( strlen( $m ) > 5 ) {
					$start['monthnum'] = substr( $m, 4, 2 );
					$end['year'] = $start['year'];
					$end['monthnum'] = $start['monthnum'] + 1;
				}

				if ( strlen( $m ) > 7 ) {
					$start['day'] = substr( $m, 6, 2 );
					$end['monthnum'] = $start['monthnum'];
					$end['day'] = $start['day'] + 1;
				}
				
				if ( strlen( $m ) > 9 ) {
					$start['hour'] = substr( $m, 8, 2 );
					$end['day'] = $start['day'];
					$end['hour'] = $start['hour'] + 1;
				}
				
				if ( strlen( $m ) > 11 ) {
					$start['minute'] = substr( $m, 10, 2 );
					$end['hour'] = $start['hour'];
					$end['minute'] = $start['minute'] + 1;
				}
				
				if ( strlen( $m ) > 13 ) {
					$start['second'] = substr( $m, 12, 2 );
					$end['minute'] = $start['minute'];
					$end['second'] = $start['second'] + 1;
				}
			}
			
		} else if ( isset( $wp_query->query_vars['year'] )
			&& ! empty( $wp_query->query_vars['year'] ) 
			&& ( (int) $wp_query->query_vars['year'] < 1700 ) ) {
			
			$conversion = true;
			$start['year'] = $wp_query->query_vars['year'];
			$end['year'] = $start['year'] + 1;

			if ( isset( $wp_query->query_vars['monthnum'] ) 
				&& ! empty( $wp_query->query_vars['monthnum'] ) ) {
					$start['monthnum'] = $wp_query->query_vars['monthnum'];
					$end['year'] = $start['year'];
					$end['monthnum'] = $start['monthnum'] + 1;
			}
			
			if ( isset( $wp_query->query_vars['day'] ) 
				&& ! empty( $wp_query->query_vars['day'] ) ) {
					$start['day'] = $wp_query->query_vars['day'];
					$end['monthnum'] = $start['monthnum'];
					$end['day'] = $start['day'] + 1;
			}
			
			if ( isset( $wp_query->query_vars['hour'] ) 
				&& ! empty( $wp_query->query_vars['hour'] ) ) {
					$start['hour'] = $wp_query->query_vars['hour'];
					$end['day'] = $start['day'];
					$end['hour'] = $start['hour'] + 1;
			}
			
			if ( isset( $wp_query->query_vars['minute'] ) 
				&& ! empty( $wp_query->query_vars['minute'] ) ) {
					$start['minute'] = $wp_query->query_vars['minute'];
					$end['hour'] = $start['hour'];
					$end['minute'] = $start['minute'] + 1;
			}
			
			if ( isset( $wp_query->query_vars['second'] ) 
				&& ! empty( $wp_query->query_vars['second'] ) ) {
					$start['second'] = $wp_query->query_vars['second'];
					$end['minute'] = $start['minute'];
					$end['second'] = $start['second'] + 1;
			}
		}
		
		if ( ! $conversion )
			return $where;
	
		$where = self::strip_date_clauses( $where );
		
		if ( $end['second'] > 59 ) {
			$end['second'] = 0;
			$end['minute']++;
		}
		
		if ( $end['minute'] > 59 ) {
			$end['minute'] = 0;
			$end['hour']++;
		}
		
		if ( $end['hour'] > 23 ) {
			$end['hour'] = 0;
			$end['day']++;
		}
		
		if ( $end['day'] > $days_in_month[$start['monthnum']-1] ){
			$end['day'] = 1;
			$end['monthnum']++;
		}
		
		if ( $end['monthnum'] > 12 ) {
			$end['monthnum'] = 1;
			$end['year']++;
		}
		
		$start_date = date( 'Y-m-d H:i:s', self::mktime( $start['hour'], $start['minute'], $start['second'], $start['monthnum'], $start['day'], $start['year'] ) );
		$end_date = date( 'Y-m-d H:i:s', self::mktime( $end['hour'], $end['minute'], $end['second'], $end['monthnum'], $end['day'], $end['year'] ) );
		
		$where .= " AND $wpdb->posts.post_date >= '$start_date' AND $wpdb->posts.post_date < '$end_date' ";
		return $where;
	}
	
	public static function strip_date_clauses( $where )
	{
		$patterns = array(
			"YEAR\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{4}'*",
			"DAYOFMONTH\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"MONTH\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"HOUR\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"MINUTE\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
			"SECOND\(\s*.*post_date\s*\)\s*=\s*'*[0-9]{1,}'*",
		);
		
		foreach ( $patterns as $pattern )
			$where = preg_replace( '/'.$pattern.'/', '1=1', $where ); 
		
		return $where;
	}
	
	public static function get_archives( $r = '' )
	{
		global $wpdb, $wp_locale;

		$defaults = array(
			'type' => 'monthly',
			'limit' => '',
			'format' => 'html',
			'before' => '',
			'after' => '',
			'show_post_count' => false,
			'echo' => 1,
			'order' => 'DESC',
		);

		$args = wp_parse_args( $r, $defaults );
		//extract( $r, EXTR_SKIP );

		if ( '' == $args['type'] )
			$args['type'] = 'monthly';

		if ( '' != $args['limit'] ) {
			$args['limit'] = absint( $args['limit'] );
			$args['limit'] = ' LIMIT '.$args['limit'];
		}

		$args['order'] = strtoupper( $args['order'] );
		if ( $args['order'] !== 'ASC' )
			$args['order'] = 'DESC';

		// this is what will separate dates on weekly archive links
		$archive_week_separator = '&#8211;';

		// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
		$archive_date_format_over_ride = 0;

		// options for daily archive (only if you over-ride the general date format)
		$archive_day_date_format = 'Y/m/d';

		// options for weekly archive (only if you over-ride the general date format)
		$archive_week_start_date_format = 'Y/m/d';
		$archive_week_end_date_format	= 'Y/m/d';

		if ( !$archive_date_format_over_ride ) {
			$archive_day_date_format = get_option( 'date_format' );
			$archive_week_start_date_format = get_option( 'date_format');
			$archive_week_end_date_format = get_option( 'date_format' );
		}

		$where = apply_filters( 'getarchives_where', "WHERE post_type = 'post' AND post_status = 'publish'", $args );
		$join = apply_filters( 'getarchives_join', '', $args );
		
		$where = self::strip_date_clauses( $where ); // just in case!
		
		$days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$output = '';
		$last_persian_year = $last_persian_month = false;
		$afterafter = $args['after'];
		$limit = 1;
					
		$last_changed = wp_cache_get( 'last_changed', 'posts' );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'posts' );
		}

		if ( 'monthly' == $args['type'] ) {	
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `day` FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAY(post_date) ORDER BY post_date ".$args['order']; //.' '.$args['limit'];
			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( 0 == $result->year )
							continue;
							
						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_persian_month = self::date( 'Ym', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
						
						if ( $last_persian_month != $the_persian_month ) {
							$the_year = self::date( 'Y', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
							$the_month = self::date( 'm', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
							$url = get_month_link( $the_year, $the_month );
							$text = sprintf( _x( '%1$s %2$s', 'wp_get_archives monthly', GPERSIANDATE_TEXTDOMAIN ), 
								self::get_month( $the_month ), 
								self::translate_numbers( $the_year )
							);
							if ( $args['show_post_count'] ) {
								//$first_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/'.$the_month.'/'.'01' ) );
								//$last_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/'.$the_month.'/'.gPersianDateHelper::j_last_day_of_month( $the_month ) ) );
								$first_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $the_month, 1, $the_year ) );
								$last_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $the_month, $days_in_month[$the_month-1], $the_year ) );								
								$post_count = $wpdb->get_results( "SELECT COUNT(id) as 'post_count' FROM $wpdb->posts $join $where AND post_date >='$first_day' AND post_date <='$last_day' ");
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives monthly count', GPERSIANDATE_TEXTDOMAIN ),
									self::translate_numbers( $post_count[0]->post_count ) ).$afterafter;
							}
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
							if ( $limit == $args['limit'] ) 
								break;
							$limit++;
						}
						$last_persian_month = $the_persian_month;
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );								
				}				
			} else {
				$output .= $results;
			}			
		} elseif ( 'yearly' == $args['type'] ) {
			$query = "SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAY(post_date) AS `dayofmonth` FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date ".$args['order'];;
			$key = md5( $query.'_'.$args['limit'] );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( 0 == $result->year )
							continue;
						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_persian_year = self::date( 'Y', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
						if ( $last_persian_year != $the_persian_year ) {
							$the_year = self::date( 'Y', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
							$url = get_year_link( $the_year );
							$text = sprintf( _x( '%s', 'wp_get_archives yearly', GPERSIANDATE_TEXTDOMAIN ), 
								self::translate_numbers( $the_year )
							);
							if ( $args['show_post_count'] ) {
								//$first_day = date( 'Y-m-d', gPersianDateHelper::convert_back( $the_year.'/01/01' ) );
								//$last_day = date( 'Y-m-d', gPersianDateHelper::convert_back( ($the_year+1).'/01/01' ) );
								$first_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, 1, 1, $the_year ) );
								$last_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, 1, 1, $the_year+1 ) );								
								$post_count = $wpdb->get_results( "SELECT COUNT(id) as 'post_count' FROM $wpdb->posts $join $where AND post_date >='$first_day' AND post_date <='$last_day' ");
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives yearly count', GPERSIANDATE_TEXTDOMAIN ),
									self::translate_numbers( $post_count[0]->post_count ) ).$afterafter;
							}
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
							if ( $limit == $args['limit'] ) 
								break;
							$limit++;
						}
						$last_persian_year = $the_persian_year;
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );
				}
			} else {
				$output .= $results;
			}			
		} elseif ( 'daily' == $args['type'] ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				//$cache[ $key ] = $results;
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						$the_date = mktime( 0 ,0 , 0, zeroise( $result->month, 2 ), $result->day, $result->year );
						$the_year = self::date( 'Y', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
						$the_month = self::date( 'm', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
						$the_day = self::date( 'd', $the_date, 'UTC', GPERSIANDATE_LOCALE, false );
						$url = get_day_link( $the_year, $the_month, $the_day );
						$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
						$text = mysql2date( $archive_day_date_format, $date, true ); // this will convert the date
						if ( $args['show_post_count'] )
							$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives daily count', GPERSIANDATE_TEXTDOMAIN ),
								self::translate_numbers( $result->posts ) ).$afterafter;
						$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
					}
					$results = $output;
					wp_cache_set( $key, $results, 'posts' );
				}
			} else {
				$output .= $results;
			}			
		} elseif ( 'weekly' == $args['type'] ) {
			$week = _wp_mysql_week( '`post_date`' );
			$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				
				$arc_w_last = '';
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( $result->week != $arc_w_last ) {
							$arc_year = $result->yr;
							$arc_w_last = $result->week;
							$arc_week = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
							$arc_week_start = date_i18n( $archive_week_start_date_format, $arc_week['start'] );
							$arc_week_end = date_i18n( $archive_week_end_date_format, $arc_week['end'] );
							$url = sprintf( '%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $result->week );
							$text = $arc_week_start.$archive_week_separator.$arc_week_end;
							if ( $args['show_post_count'] )
								$args['after'] = sprintf( _x( '&nbsp;(%s)', 'wp_get_archives weekly count', GPERSIANDATE_TEXTDOMAIN ),
									self::translate_numbers( $result->posts ) ).$afterafter;
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
						}
					}
				}
				$results = $output;
				wp_cache_set( $key, $results, 'posts' );
			} else {
				$output .= $results;
			}			
		} elseif ( ( 'postbypost' == $args['type'] ) || ( 'alpha' == $args['type'] ) ) {
			$orderby = ('alpha' == $type) ? 'post_title ASC ' : 'post_date DESC ';
			$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY ".$args['order'].' '.$args['limit'];
			$key = md5( $query );
			$key = "wp_get_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				
				if ( $results ) {
					foreach ( (array) $results as $result ) {
						if ( $result->post_date != '0000-00-00 00:00:00' ) {
							$url = get_permalink( $result );
							if ( $result->post_title )
								$text = strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) );
							else
								$text = $result->ID;
							$output .= get_archives_link( $url, $text, $args['format'], $args['before'], $args['after'] );
						}
					}
				}
				$results = $output;
				wp_cache_set( $key, $results, 'posts' );
			} else {
				$output .= $results;
			}			
		}
		
		if ( $args['echo'] )
			echo $output;
		else
			return $output;			
		
	}
	
	public static function get_month( $month, $all = false )
	{
		$months = array( 
			'01' => __( 'Farvardin', GPERSIANDATE_TEXTDOMAIN ),
			'02' => __( 'Ordibehesht', GPERSIANDATE_TEXTDOMAIN ),
			'03' => __( 'Khordad', GPERSIANDATE_TEXTDOMAIN ),
			'04' => __( 'Tir', GPERSIANDATE_TEXTDOMAIN ),
			'05' => __( 'Mordad', GPERSIANDATE_TEXTDOMAIN ),
			'06' => __( 'Shahrivar', GPERSIANDATE_TEXTDOMAIN ),
			'07' => __( 'Mehr', GPERSIANDATE_TEXTDOMAIN ),
			'08' => __( 'Aban', GPERSIANDATE_TEXTDOMAIN ),
			'09' => __( 'Azar', GPERSIANDATE_TEXTDOMAIN ),
			'10' => __( 'Dey', GPERSIANDATE_TEXTDOMAIN ),
			'11' => __( 'Bahman', GPERSIANDATE_TEXTDOMAIN ),
			'12' => __( 'Esfand', GPERSIANDATE_TEXTDOMAIN ),
		);
		
		if ( $all )
			return $months;
		
		return $months[zeroise($month, 2)];
	}
	
	public static function get_dayoftheweek( $dayoftheweek, $all = false )
	{
		$week = array(
			0 => __( 'Sunday', GPERSIANDATE_TEXTDOMAIN ),
			1 => __( 'Monday', GPERSIANDATE_TEXTDOMAIN ), 
			2 => __( 'Tuesday', GPERSIANDATE_TEXTDOMAIN ), 
			3 => __( 'Wednesday', GPERSIANDATE_TEXTDOMAIN ), 
			4 => __( 'Thursday', GPERSIANDATE_TEXTDOMAIN ), 
			5 => __( 'Friday', GPERSIANDATE_TEXTDOMAIN ), 
			6 => __( 'Saturday', GPERSIANDATE_TEXTDOMAIN ), 
		);
		
		if ( $all )
			return $week;
		
		return $week[$dayoftheweek-1];
	}
	
	function _strings_for_pot()
	{
		$keywords = array(
			__( 'Sat', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Sun', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Mon', GPERSIANDATE_TEXTDOMAIN ),
			__( 'Tue', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Wed', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Thu', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Fri', GPERSIANDATE_TEXTDOMAIN ), 
			
			__( 'August', GPERSIANDATE_TEXTDOMAIN ),
			__( 'Aug', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'September', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Sep', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'October', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Oct', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'November', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Nov', GPERSIANDATE_TEXTDOMAIN ),
			__( 'December', GPERSIANDATE_TEXTDOMAIN ),
			__( 'Dec', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'January', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Jan', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'February', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Feb', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'March', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Mar', GPERSIANDATE_TEXTDOMAIN ),
			__( 'April', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Apr', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'May', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'June', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Jun', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'July', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Jul', GPERSIANDATE_TEXTDOMAIN ),
			__( 'Today', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Yesterday', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Tomorrow', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Next', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Last', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Previous', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Year', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Month', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Week', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Day', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Hour', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Minute', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'Second', GPERSIANDATE_TEXTDOMAIN ),
			__( 'st', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'nd', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'rd', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'th', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'am', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'AM', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'pm', GPERSIANDATE_TEXTDOMAIN ), 
			__( 'PM', GPERSIANDATE_TEXTDOMAIN ),
		);

		$numbers = array ( 
			__( '1', GPERSIANDATE_TEXTDOMAIN ),
			__( '2', GPERSIANDATE_TEXTDOMAIN ),
			__( '3', GPERSIANDATE_TEXTDOMAIN ),
			__( '4', GPERSIANDATE_TEXTDOMAIN ),
			__( '5', GPERSIANDATE_TEXTDOMAIN ),
			__( '6', GPERSIANDATE_TEXTDOMAIN ),
			__( '7', GPERSIANDATE_TEXTDOMAIN ),
			__( '8', GPERSIANDATE_TEXTDOMAIN ),
			__( '9', GPERSIANDATE_TEXTDOMAIN ),
			__( '10', GPERSIANDATE_TEXTDOMAIN ),
			__( '11', GPERSIANDATE_TEXTDOMAIN ),
			__( '12', GPERSIANDATE_TEXTDOMAIN ),
			__( '13', GPERSIANDATE_TEXTDOMAIN ),
			__( '14', GPERSIANDATE_TEXTDOMAIN ),
			__( '15', GPERSIANDATE_TEXTDOMAIN ),
			__( '16', GPERSIANDATE_TEXTDOMAIN ),
			__( '17', GPERSIANDATE_TEXTDOMAIN ),
			__( '18', GPERSIANDATE_TEXTDOMAIN ),
			__( '19', GPERSIANDATE_TEXTDOMAIN ),
			__( '20', GPERSIANDATE_TEXTDOMAIN ),
			__( '21', GPERSIANDATE_TEXTDOMAIN ),
			__( '22', GPERSIANDATE_TEXTDOMAIN ),
			__( '23', GPERSIANDATE_TEXTDOMAIN ),
			__( '24', GPERSIANDATE_TEXTDOMAIN ),
			__( '25', GPERSIANDATE_TEXTDOMAIN ),
			__( '26', GPERSIANDATE_TEXTDOMAIN ),
			__( '27', GPERSIANDATE_TEXTDOMAIN ),
			__( '28', GPERSIANDATE_TEXTDOMAIN ),
			__( '29', GPERSIANDATE_TEXTDOMAIN ),
			__( '30', GPERSIANDATE_TEXTDOMAIN ),
			__( '31', GPERSIANDATE_TEXTDOMAIN )
		);
	}
	
	/**
		TODO: 
		args
		styles
		cache
		shortcode
		widget
	**/
	public static function get_compact( $r = '' )
	{
		global $wpdb;
		$args = array();
		$output = '';
		$days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$where = apply_filters( 'getarchives_where', "WHERE post_type = 'post' AND post_status = 'publish'", $args );
		$join = apply_filters( 'getarchives_join', '', $args );
		$where = self::strip_date_clauses( $where ); // just in case!
		
		$first = $wpdb->get_results("SELECT post_date AS date FROM $wpdb->posts $where AND post_password='' $join ORDER BY post_date ASC LIMIT 1");
		if ( $first ) {
			$last = $wpdb->get_results("SELECT post_date AS date FROM $wpdb->posts $where AND post_password='' $join ORDER BY post_date DESC LIMIT 1");
			//$the_year = self::date( 'Y', current_time( 'timestamp' ), 'UTC', GPERSIANDATE_LOCALE, false );
			$the_year = self::date( 'Y', strtotime( $last[0]->date ), 'UTC', GPERSIANDATE_LOCALE, false );
			$year = self::date( 'Y', strtotime( $first[0]->date ), 'UTC', GPERSIANDATE_LOCALE, false );
			//echo $results[0]->date.'<br />';
			//echo 't:'.$the_year.'<br />';
			//echo 'y:'.$year.'<br />';
			while ( $the_year >= $year ) {
				$output .= '<div dir="ltr"><a href="'.get_year_link( $year ).'">'.self::translate_numbers( $year ).'</a> : ';
				for ( $month = 1; $month <= 12; $month += 1) {
					$first_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $month, 1, $year ) );
					$last_day = date( 'Y-m-d H:i:s', self::mktime( 0, 0, 0, $month, $days_in_month[$month-1], $year ) );
					$results = $wpdb->get_results("SELECT post_date AS date FROM $wpdb->posts $where AND post_password='' AND post_date >='$first_day' AND post_date <='$last_day' $join LIMIT 1");
					//$text = self::translate_numbers( zeroise( $month, 2 ) );
					$text = self::translate_numbers( $month );
					//$text = self::get_month( $month );
					if ( $results )
						$output .= '<span><a href="'.get_month_link( $year, $month ).'">'.$text.'</a></span>&nbsp;';
					else
						$output .= '<span class="empty" style="opacity:0.4;">'.$text.'</span>&nbsp;';
				}
				$output .= '</div>';
				$year++;
			}
		} else {
			$output = __( 'Archives are empty.', GPERSIANDATE_TEXTDOMAIN );
		}
		
		return $output;
		
	}
	
	function day_link( $link, $year, $month, $day ) 
	{ 
		$current_time = current_time( 'timestamp' );
		$current_year = gmdate( 'Y', $current_time );
		if ( $year == $current_year )
			return str_replace( 
				array( $year, $month, $day ),
				array( 
					self::date( 'Y', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ),
					self::date( 'm', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ),
					self::date( 'd', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ), 
				),
				$link
			);
		return $link;
	}
	
	// ISSUE : must convert year/month back from filter args!
	function month_link( $link, $year, $month ) 
	{ 
		$current_time = current_time( 'timestamp' );
		$current_year = gmdate( 'Y', $current_time );
		if ( $year == $current_year )
			return str_replace( 
				array( $year, $month ),
				array( 
					self::date( 'Y', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ),
					self::date( 'm', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ),
				),
				$link
			);
		return $link;
	}
	
	function year_link( $link, $year ) 
	{
		$current_time = current_time( 'timestamp' );
		$current_year = gmdate( 'Y', $current_time );
		if ( $year == $current_year )
			return str_replace( $year, self::date( 'Y', $current_time, 'UTC', GPERSIANDATE_LOCALE, false ), $link );
		return $link;
	}
	
	function post_link( $permalink, $post, $leavename )
	{
		if ( false !== strpos( $permalink, '?p=' ) )
			return $permalink;
		
		if ( in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
			return $permalink;
		
		$permalink_structure = apply_filters( 'pre_post_link', get_option( 'permalink_structure' ), $post, $leavename );
		if ( ! $permalink_structure ) 
			return $permalink;
		
		if ( false === strpos( $permalink_structure, '%year%' )
			&& false === strpos( $permalink_structure, '%monthnum%' ) 
			&& false === strpos( $permalink_structure, '%day%' ) )
				return $permalink;
		
		$category = '';
		if ( false !== strpos( $permalink_structure, '%category%' ) ) {
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				usort( $cats, '_usort_terms_by_ID'); // order by ID
				$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );
				$category_object = get_term( $category_object, 'category' );
				$category = $category_object->slug;
				if ( $parent = $category_object->parent )
					$category = get_category_parents( $parent, false, '/', true ).$category;
			}
			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty( $category ) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
			}
		}

		$author = '';
		if ( false !== strpos( $permalink_structure, '%author%' ) ) {
			$authordata = get_userdata( $post->post_author );
			$author = $authordata->user_nicename;
		}
		
		$date = explode( " ", self::date( 'Y m d H i s', strtotime( $post->post_date ), 'UTC', GPERSIANDATE_LOCALE, false ) );
		
		$rewritereplace = array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename ? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename ? '' : '%pagename%',
		);		
		
		$permalink = home_url( str_replace( $rewritecode, $rewritereplace, $permalink_structure ) );
		return user_trailingslashit( $permalink, 'single' );		
	}
	
	public function wp_title_parts( $title_array )
	{
		if ( is_archive() ) {
			
			$m = get_query_var( 'm' );
			$year = get_query_var( 'year' );
			$title = '';
			$t_sep = '%WP_TITILE_SEP%'; // temporary separator
			
			if ( ! empty( $m ) ) {
				$my_year = substr( $m, 0, 4 );
				$my_month = self::get_month( substr( $m, 4, 2 ) );
				$my_day = intval( substr( $m, 6, 2 ) );
				$title = $my_year.( $my_month ? $t_sep.$my_month : '' ).( $my_day ? $t_sep.$my_day : '' );
			}

			if ( ! empty( $year ) ) {
				$title = $year;
				$monthnum = get_query_var( 'monthnum' );
				$day = get_query_var( 'day' );
				
				if ( ! empty( $monthnum ) )
					$title .= $t_sep.self::get_month( $monthnum );
				
				if ( ! empty( $day ) )
					$title .= $t_sep.zeroise( $day, 2 );
			}
			
			if ( $title )
				return explode( $t_sep, self::translate_numbers( $title ) );
		}
		return $title_array;
	}
	
	public function pre_insert_term( $term, $taxonomy )
	{
		if ( ! is_int( $term ) )
			return self::translate_numbers( $term );
		return $term;
	}
	
	function gshop_stats_current_month( $month, $current, $force_iso )
	{
		$date = ExtDateTime::factory( 'Persian', $current, 'UTC', array( & $this, 'translator' ) );
		//return self::translate_numbers( $date->format( self::translate_format( $req_format ) ) );
		return $date->format( 'Y_m' );
	}
	
	/**
	 * Transforms the WP_Locale translations for the wp.locale JavaScript class.
	 *
	 * Used by P2 and WordPress.com support forums.
	 *
	 * @param $locale WP_Locale - A locale object.
	 * @param $json_encode bool - Whether to encode the result. Default true.
	 * @return string|array     - The translations object.
	 */
	// localized version of P2's get_js_locale()
	public static function getJSLocale( $locale, $json_encode = true ) 
	{
		$months = array_values( self::get_month( null, true ) );
		
		$js_locale = array(
			//'month'         => array_values( $locale->month ),
			'month'         => $months,
			//'monthabbrev'   => array_values( $locale->month_abbrev ),
			'monthabbrev'   => $months,
			//'weekday'       => array_values( $locale->weekday ),
			'weekday'       => array_values( self::get_dayoftheweek( null, true ) ),
			'weekdayabbrev' => array_values( $locale->weekday_abbrev ),
		);

		if ( $json_encode )
			return json_encode( $js_locale );
		else
			return $js_locale;
	}
}