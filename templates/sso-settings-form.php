<?php 
/**
 * The Template for displaying Helprace Feedback settings form in admin 
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Helprace Feedback 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Flush rewrite rule when helprace logout url not exists
if ( ! get_option( 'helprace_flush_rules' ) ) {
   flush_rewrite_rules();
   update_option( 'helprace_flush_rules', true );
}


// Get feedback settings in option table exists else default settings
$settings = (array)get_option( 'helprace_sso_options', array() );
$feedback_settings = get_option( 'helprace_options', array() );
if( !isset( $settings['hfbw_name'] ) && isset($feedback_settings['hfbw_name']))
$settings['hfbw_name'] = $feedback_settings['hfbw_name'];
$clean_url = true;
if( ! get_option('permalink_structure') ){
  echo '<div class="updated error"><p>'.__('<strong>Error:</strong> Remote Logout URL need to use preety permalink enable on your site.', 'helprace' ).'</p></div>';  
  $clean_url = false;
} 


?>
<div class="wrap">
  <h2>
    <?php _e( 'Helprace Single Sign-On(SSO) Settings', 'helprace' ); ?>
  </h2>
  <?php settings_errors(); ?>
  <p>
    <?php _e( 'Helprace Single Sign-On allows users can authenticate to your systems and get logged in to your Helprace portal automatically.', 'helprace' ); ?>
  </p> 
  <form method="post" action="options.php" style="margin-top:20px;">
    <?php settings_fields( self::$sso_page_slug );	?>
    <table class="form-table">
      <tr>
        <th><label for="eedback_widget_enablef">
            <?php _e( 'Single Sign-On (SSO)', 'helprace' ); ?>
          </label></th>
        <td>
           <fieldset>
            <label>
              <input type="checkbox" name="helprace_sso_options[hfbw_display]" value="enable" <?php checked( $settings['hfbw_display'], 'enable' ); ?> />
              <span class="description"><?php _e( 'Enable single sign-on on your site.', 'helprace' ); ?></span>
            </label>
           </fieldset>
          </td>
      </tr>
      <tr>
        <th><label for="domain_name">
            <?php _e( 'Helprace Subdomain', 'helprace' ) ?>
            <span class="required">*</span></label></th>
        <td><input type="text" name="helprace_sso_options[hfbw_name]" id="hfbw_name" value="<?php echo esc_attr( $settings['hfbw_name'] ); ?>"/>
          <input type="text" id="subdomaintwo" value=".helprace.com" disabled/>
          <?php if( $settings['hfbw_name'] ): ?>
          <span><a href="<?php echo esc_url( set_url_scheme( $settings['hfbw_name'].'.helprace.com' ) ); ?>" class="button-secondary" target="_blank">
          <?php _e( 'Visit Website', 'helprace' ); ?>
          </a></span>
          <?php endif; ?>
          <p class="description">
            <?php printf( __( 'Enter your Helprace account name (same as your subdomain). E.g.: %s', 'helprace' ), '<strong>acme</strong>.helprace.com' ); ?>
            <br />
            <?php printf( __( '%sSign up here%s is you do not have a Helprace account yet. ', 'helprace' ), '<a href="http://helprace.com/signup" target="_blank">', '</a>'); ?>
          </p></td>
      </tr>
      <tr>
         <th><label for="security_key"><?php _e( 'Security Key', 'helprace' ); ?>  <span class="required">*</span></label></th>
         <td><input type="text" id="security_key" name="helprace_sso_options[sso_key]" value="<?php echo esc_attr( $settings['sso_key'] );  ?>" class="regular-text" /></td>
      </tr>
    </table>
    <h2 class="sso-setting">
      <?php _e('Helprace Configration'); ?>
    </h2>
    <p><?php _e( 'The settings that need to be configured in your Helprace account.', 'helprace'); ?></p>
    <table class="form-table">
      <tr>
         <th>
            <label><?php _e( 'Remote Login URL', 'helprace'); ?></label>
         </th>
         <td><code><?php echo wp_login_url().'?helprace_action=remotelogin'; ?></code></td>
      </tr>
      <?php if( $clean_url ) : ?>
      <tr>
         <th>
            <label><?php _e( 'Remote Logout URL', 'helprace'); ?></label>
         </th>
         <td>
           <?php
		   ?>
           <code id="sso_logout_url"><?php echo site_url('remotelogout/'); ?></code>
         </td>
      </tr>
     <?php endif; ?> 
    </table>
    <?php do_settings_sections( self::$sso_page_slug ); ?>
    <?php submit_button(); ?>
  </form>
</div>
