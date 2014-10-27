<?php defined( 'ABSPATH' ) or die( 'Restricted access' );
if ( ! class_exists( 'gPersianDateSettingsCore' ) ) { class gPersianDateSettingsCore
{  
    var $_enabled = false;
    //var $_settings = array();
	var $_plugin_constants = array();
    var $_settings_args = array(
        'plugin_class' => false,
        'option_group' => 'gpluginsettings',
		'page' => 'general',
        'sections' => array( 
			'default' => array( 
			//'date' => array( 
				'title' => false,
				//'title' => 'Section Title',
				'callback' => array( __CLASS__, 'section_callback' ), // '__return_false'
				'fields' => array(
					'enabled' => array(
						'title' => 'gPersianDate',
						'desc' => 'select to enable Persian date for WordPress',
						'type' => 'enabled',
						'dir' => 'ltr',
						'default' => 0,
						'filter' => false, // 'esc_attr'
					),
				),
			),
		),
    );
	
	
    function __construct( $plugin_constants = array(), $settings_args = array() )
	{
		$options = self::get_options();
		$this->_plugin_constants = $plugin_constants;
		$this->_settings_args = array_merge( $this->_settings_args, $settings_args );
		$this->_enabled = isset( $options['enabled'] ) ? $options['enabled'] : false;
		
		//$plugin_class = $this->_settings_args['plugin_class'];
		//if ( $this->_enabled && class_exists( $plugin_class ) ) 
			//$plugin_instance = & $plugin_class::getInstance( $options );

		add_action( 'admin_init', array( &$this, 'admin_init' ) );	
    }
    
	function admin_init()
	{
		if ( ! count( $this->_settings_args['sections'] ) )
			return;
			
        $page = ( $this->_settings_args['page'] ? $this->_settings_args['page'] : 'general' );
        //$section = ( $this->_settings_args['section'] ? $this->_settings_args['section'] : 'default' );
        
		register_setting( $page,
            $this->_settings_args['option_group'],
            array( $this, 'settings_sanitize' )
        ); 
        
		foreach ( $this->_settings_args['sections'] as $section_name => $section_args ) {
			if ( $section_args['title'] )
				add_settings_section( $section_name, $section_args['title'], $section_args['callback'],	$page );
			
			foreach ( $section_args['fields'] as $field_name => $field_args ) {
				$field_id = $this->_settings_args['option_group'].'_'.$field_name;
				add_settings_field( $field_id, $field_args['title'], array( $this, 'do_settings_field' ), $page, $section_name, 
					array_merge( $field_args, array( 'field' => $field_name, 'label_for' => $field_id ) ) );
			}
		}
	}
	
	function do_settings_field( $args )
	{
		$options = self::get_options();
		$name = $this->_settings_args['option_group'].'['.$args['field'].']';
		switch ( $args['type'] ) {
			case 'enabled' :
				?><select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
					<option value="0" <?php selected( $options[$args['field']], 0 ); ?>><?php esc_html_e( 'Disabled' ); ?></option>
					<option value="1" <?php selected( $options[$args['field']], 1 ); ?>><?php esc_html_e( 'Enabled' ); ?></option>
				</select><?php
			break;
			case 'text' :
				?><input type="text" class="regular-text code" 
					name="<?php echo $name; ?>" id="<?php echo $name; ?>"
					value="<?php echo esc_attr( $options[$args['field']] ); ?>"
					dir="<?php echo $args['dir']; ?>"/><?php
			break;
			default :
				echo 'Error: setting type\'s not defind';
		}
		if ( isset( $args['desc'] ) )
			echo ' <span class="description">'.esc_html( $args['desc'] ).'</span>';
	}

    function get_option( $field, $default = false )
	{
		$options = self::get_options();
		if ( isset( $options[$field] ) )
			return $options[$field];
		return $default;
	}
	
    function get_options()
    {
        return get_option( $this->_settings_args['option_group'], self::get_option_defaults() );
    }
    
    function get_option_defaults()
    {
		$defaults = array();
		foreach ( $this->_settings_args['sections'] as $section_name => $section_args )
			foreach ( $section_args['fields'] as $field_name => $field_args )
				$defaults[$field_name] = $field_args['default'];
		return (array) apply_filters( $this->_settings_args['option_group'].'_option_defaults', $defaults );
    }
    
    function settings_sanitize( $input )
    {
	
		// must use call func 
		
		$output = array();
        $defaults = self::get_option_defaults();
        foreach( $defaults as $field => $default )
            if ( isset( $input[$field] ) )
                $output[$field] = $input[$field];
			else
				$output[$field] = $default;
        return $output;
    }
    
    function section_callback( $section )
    {
        echo '<p>Section Description</p>';
    }
} 
}