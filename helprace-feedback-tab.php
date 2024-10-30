<?php 
/**
 * Plugin Name: Helprace Feedback Tab & SSO
 * Plugin URI: https://www.example.com/
 * Description: Add Helprace feedback tab widget on your site 
 * Version: 2.0
 * Author: Helprace
 * Author URI: https://helprace.com/
 * Text Domain: helprace
 * License: GPLv2 or later
*/

if( ! defined( 'ABSPATH' ) ) exit();

/**
 * Define Helprace Feedback Constants.
 */
 
define( 'HRFW_VERSION', '2.0' );
define( 'HRFW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HRFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include required core files used in plugin.
 */
  require_once( HRFW_PLUGIN_DIR . 'includes/functions.php' );
  require_once( HRFW_PLUGIN_DIR . 'includes/helprace-jwt.php' );
 

/**
 * Include required core files used in admin.
 */
 
if( is_admin() ){
  require_once( HRFW_PLUGIN_DIR . 'helprace-admin.php' );
  Helprace_Admin::get_instance();
}

/**
 * Register plugin activation method
 */
 
register_activation_hook( __FILE__, 'helprace_on_activation' );
function helprace_on_activation(){
	// Add new rewrite rule
	helprace_add_query_rewrite();
	flush_rewrite_rules();
	update_option( 'helprace_flush_rules', true );
} 

/**
 * Initialize language files
 */
 
function helprace_feedback_init_lang(){
  load_plugin_textdomain('helprace', false, dirname( plugin_basename( __FILE__ ) ). '/lang/');	
}
add_action('plugins_loaded', 'helprace_feedback_init_lang');


/**
 * Display feedback widget fronted.
 */
 
function helprace_feedback_footer_init(){
  helprace_feedback_widget_display( (array)get_option( 'helprace_options' ) ); 	
}
add_action( 'wp_footer', 'helprace_feedback_footer_init', 5000 );

/**
 * Single signon authorization in wp system.
 */
 

add_action( 'init', 'helprace_sso_authorization' );

function helprace_sso_authorization(){
	// Single sign on process initialization  
	if( isset( $_REQUEST['helprace_action'] ) && in_array( $_REQUEST['helprace_action'], array( 'remotelogin', 'remotelogout' ) ) ){
	  $wp_error = new WP_Error();
	  $sso_settings = get_option( 'helprace_sso_options', array() );
	  // SSO work out when it disabled
	  if( !isset( $sso_settings['hfbw_display'] ) || $sso_settings['hfbw_display'] != 'enable' || defined( 'DOING_AJAX' ) ){
		$wp_error->add( 'sso_disabled', __( 'Single sign-on is disabled in Wordpress settings.', 'helprace' ) );
		login_header( __( 'Single sign-on' ), '', $wp_error );
		login_footer();
		die();  
	  }
	  $subdomain = esc_attr( $sso_settings['hfbw_name']);
	  $security_key = esc_attr( $sso_settings['sso_key']);
	  // Exit when subdomain is not configure in setting
	  if( empty( $subdomain ) ){
		$wp_error->add( 'sso_subdomain', __( 'Single sign-on is not configured for this Helprace subdomain in Wordpress settings.', 'helprace' ) );
		login_header( __( 'Single sign-on' ), '', $wp_error );
		login_footer();
		die();  
 	  }
	  // Exit when security key is not configure in setting
	  if( empty($security_key) ){
		$wp_error->add( 'sso_subdomain', __( 'Single sign-on is not configured for this Helprace security key in Wordpress settings.', 'helprace' ) );
		login_header( __( 'Single sign-on' ), '', $wp_error );
		login_footer();
		die();  
	  }
	  $crt = $_REQUEST['crt'] ? $_REQUEST['crt'] : time();
	  $return_to = $_REQUEST['return_to'] ? esc_url($_REQUEST['return_to']) : '';
	   if(  $_REQUEST['helprace_action'] == 'remotelogin' ){
		  if( is_user_logged_in() ){
	        $user = wp_get_current_user();
			if ( $user->user_firstname != '' && $user->user_lastname != '' ) {
              $name = $user->user_firstname . ' ' . $user->user_lastname;
            } else {
              $name = $user->display_name;
            }

			$token = array(
			  "jti"                 =>  uniqid('', true), // Unique id
			  "iat"                 =>  $crt,  // Current time
			  "email"               =>  $user->user_email, // Current user email
			  "name"                =>  $name, // Full name
			  // "role"                =>  "user",      // Role could be: user, agent, admin, owner. If user already exists in, the role will be rewritten. If not specified for a new user, "user" is assumed.
			  "external_id"         =>  $user->ID    // External ID can be used to uniquely identify user. If not specified, email is used as a unique user ID.
			);
		   
		    $jwt = Helprace_JWT::encode($token, $security_key );
			$api_auth = "https://auth.helprace.com/jwt/" . $subdomain ."?jwt=" . $jwt; // Helprace auth uri
/*			if( $return_to )
			$api_auth .= '&return_to='.$return_to;
*/			wp_redirect( $api_auth );
			die();	  
		  
		  }else{
			// Redirect to login page, when user is not logged in   
			$redirct_to = wp_login_url( wp_login_url().'?helprace_action=remotelogin&crt'.$crt );  
			wp_redirect( $redirct_to );
			die();  
		  }
	   }
       if(  $_REQUEST['helprace_action'] == 'remotelogout' ){
         $redirect = $return_to ? wp_logout_url(  $return_to ) : wp_logout_url();
         wp_redirect( htmlspecialchars_decode($redirect) );
         die();
       }
		
	}
 	// Logout helprace single sign on 

		
}


/**
 * Register new query var
 */

add_filter('query_vars', 'helprace_query_vars');

function helprace_query_vars($vars){
	$vars[] = 'helprace_action';
    return $vars;
}

/**
 * Register new rewrite rule for remote logout
 */
 
add_action( 'init', 'helprace_add_query_rewrite' );

function helprace_add_query_rewrite(){
  
  add_rewrite_rule( 'remotelogout/?$', 'index.php?helprace_action=remotelogout', 'top' );
}

/**
 * Current user logout when logout from helprace
 */

add_action( 'template_redirect', 'helprace_remote_logout' );

function helprace_remote_logout(){
  // Immidiate logout when logout from helprace portal	
  if( get_query_var('helprace_action') == 'remotelogout' ){
	 wp_logout(); 
	 $redirect = isset($_REQUEST['return_to']) ? esc_url( $_REQUEST['return_to'] )  : wp_login_url();
	 wp_redirect( $redirect );
	 die(); 
  }
}


