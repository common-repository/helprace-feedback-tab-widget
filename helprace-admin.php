<?php 
/**
 * Helprace Feedback Admin Class.
 *
 * @class Helprace_Admin
 */
 
class Helprace_Admin {
	
	private static $page_slug = 'helprace-feedback-tab'; // Admin feedback setting page slug
	
	private static $sso_page_slug = 'helprace-sso'; // Admin SSO setting page slug

	
	/**
	 * The single instance of the class.
	 *
	 * @var Helprace_Admin
	 */
     
	private static $instance = null; 
	
	/**
	 * Class Helprace_Admin Constructor.
	 */
     
	public function __construct(){
	  
	  /**
	   * Initialize hook.
	   */	
       
	  add_action( 'init',  array( $this, 'init') );	
	
	}
	
	/**
	 * Admin Class.
	 *
	 * Single instance of class loaded once.
	 * @static
	 * @return Helprace_Admin - instance.
	 */
     
	public static function get_instance(){
	  
	  if( is_null( self::$instance ) )
	  self::$instance = new self();	// Get class instance 
	  return self::$instance;
	}
	
	/**
	 * Register wordpress hooks filters and actions.
	 */
     
	public function init(){
	
	  $this->init_hooks();	
		
	}
	
    /**
	 * Hook into actions and filters.
	 */
     
	private function init_hooks(){
	  add_filter( 'plugin_action_links_'.plugin_basename( HRFW_PLUGIN_DIR . 'helprace-feedback-tab.php'), array( $this , 'plugin_setting_link' ) );
	  add_action( 'admin_menu', array( $this, 'admin_menu') );	
	  add_action( 'admin_init', array( $this, 'admin_init') );
	  add_action( 'admin_enqueue_scripts', array( $this, 'settings_enqueue_scripts') );
	}

	/**
	 * Show action links on the plugin page.
	 *
	 * @param	array $links Plugin Action links
	 * @return	array
	 */
     
	public function plugin_setting_link( $links ){
	  $setting_link = array( '<a href="'. esc_url( admin_url( 'options-general.php?page='.self::$page_slug ) ) .'">' .__( 'Settings', 'helprace' ). '</a>' );
	  return array_merge( $setting_link, $links );
	}

    /**
	 * Add admin menu item in admin Settings.
	 *
	 */	
     
	public function admin_menu(){
	  // Create a new admin menu item in settings tab.	
	 add_menu_page( 
	      __( 'Helprace', 'helprace' ),
		  __( 'Helprace', 'helprace' ),
		  'manage_options',
		  self::$page_slug,
		  array( $this, 'feedback_settings_page'),
		  HRFW_PLUGIN_URL.'assets/icon.svg'
	  );
	  add_submenu_page( 
	      self::$page_slug,
	      __( 'Feedback Tab', 'helprace' ),
		  __( 'Feedback Tab', 'helprace' ),
		  'manage_options',
		  self::$page_slug,
		  array( $this, 'feedback_settings_page')
	  );
	  add_submenu_page( 
	      self::$page_slug,
	      __( 'SSO Auth', 'helprace' ),
		  __( 'SSO Auth', 'helprace' ),
		  'manage_options',
		  self::$sso_page_slug,
		  array( $this, 'sso_settings_page')
	  );

	  // 			  
	}

    /**
	 * Register settings option item.
	 */
     
	public function admin_init(){
        
		register_setting(
          self::$page_slug,  // settings section current page slug name
          'helprace_options', // setting name
		  array( $this, 'validate_settings') // validate callback
        );
		register_setting(
          self::$sso_page_slug,  // settings section sso page slug name
          'helprace_sso_options', // setting name
		  array( $this, 'validate_settings') // validate callback
        );

	}
	
	/**
	 * Enqueue scripts and styles in setting page.
	 *
	 */
     
	public function settings_enqueue_scripts($hook){
	   if( $_GET['page'] == self::$page_slug ){	
	     wp_enqueue_style( 'wp-color-picker' ); 
	     wp_enqueue_style( 'helprace-admin', HRFW_PLUGIN_URL.'assets/css/admin-style.css' );
	     wp_enqueue_script( 'helprace-admin', HRFW_PLUGIN_URL.'assets/js/admin-script.js', array('jquery', 'wp-color-picker' ) );	
	   }else if( $_GET['page'] == self::$sso_page_slug ){
	     wp_enqueue_style( 'helprace-admin', HRFW_PLUGIN_URL.'assets/css/admin-style.css' );
	   }
	}
	
	/**
	 * Display feedback settings form content.
	 *
	 */
     
	public function feedback_settings_page(){
      
	  require_once( HRFW_PLUGIN_DIR.'templates/admin-settings-form.php' );
	
	}
	
	/**
	 * Display SSO settings form content.
	 *
	 */
     
	public function sso_settings_page(){
      
	  require_once( HRFW_PLUGIN_DIR.'templates/sso-settings-form.php' );
	
	}	

    /**
	 * Validate sanitizes option's value.
	 *
	 */  
     
	public function validate_settings( $fields ){
	   if( empty( $fields['hfbw_name'] ) )
	     add_settings_error( 
		    'helprace_options', 
			'hfbw_name', 
			__( 'Insert a valid Domain Name', 'helprace' ), 
			'error' 
		 );
	   
	   if( isset( $fields['sso_key'] ) && empty( $fields['sso_key'] ) ){
	     add_settings_error( 
		    'helprace_sso_options', 
			'sso_key', 
			__( 'Security key is required for single sign-on.', 'helprace' ), 
			'error' 
		 );
	   }
	   return $fields;

    }	


}