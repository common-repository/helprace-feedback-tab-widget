<?php 
/**
 * Default settings of display feeback widget settings.
 *
 * @return Array
 */
 
function helprace_feedback_default_settings(){
	return array( 
	  'hfbw_display' => 'enable',
	  'hfbw_name'   => '',
	  'hfbw_type'   => 'tab',
	  'hfbw_space'  => '',
	  'hfbw_position' => 'left',
	  'hfbw_title' => __('Feedback & Support', 'helprace'),
	  'hfbw_bgcolor' => '#78a300',
	  'hfbw_textcolor' => '#fff',
	  'hfbw_openew_tab' => 'false',
	);  
}

/**
 * Manual function for add helprace feedback javascript and display feeback widget tab.
 *
 * @param array $args 
 */
 
function helprace_feedback_widget_display( $args ){
   $widget_display = true;
   if( ! isset( $args['hfbw_display'] ) )
   $widget_display = false;
   
   $options = wp_parse_args( $args, helprace_feedback_default_settings() );
   $account_name = $options['hfbw_name'];
   if( ! $widget_display || empty( $account_name ) )
   return false;
   $w_type = helprace_account_context($account_name);
   $feedback_type = $options['hfbw_type'] == 'tab' ? 'tab' : 'link';    // Type of feedback, link or tab
   // Set feedback config value
   $config = PHP_EOL.'configChd("feedbackType", "'.$feedback_type.'");'.PHP_EOL;
   // Config account key
   $config .=    'configChd("accountKey", Helprace.accountKey("'.$account_name.'","'.$w_type['content'].'","'.$w_type['url'].'"));'.PHP_EOL;
   
   if( ! empty( $options['hfbw_space'] ) )
	 $config .=  'configChd("space", "'.esc_html($options['hfbw_space']).'");'.PHP_EOL; // Config space
   
   // Config only feedback is type tab
   if( $feedback_type == 'tab' ){
	 $config .=  'configChd("tabTitle", "'.esc_attr($options['hfbw_title']).'");'.PHP_EOL; 
	 $config .=  'configChd("tabPosition", "'.$options['hfbw_position'].'");'.PHP_EOL;
	 $config .=  'configChd("tabBgColor", "'.$options['hfbw_bgcolor'].'");'.PHP_EOL;
	 $config .=  'configChd("tabTextColor", "'.$options['hfbw_textcolor'].'");'.PHP_EOL; 
	 $config .=  'configChd("tabAction", '.$options['hfbw_openew_tab'].');'.PHP_EOL;  
   }
 echo PHP_EOL.'<script async src="//d1culzimi74ed4.cloudfront.net/js/feedback/feedback.js"></script>'.PHP_EOL;
 echo '<script src="'.HRFW_PLUGIN_URL.'assets/js/feedback-tab.js'.'"></script>'.PHP_EOL;
 echo '<script>'.$config.'</script>';
}


/**
 * Function for get keyword content and url for specific account.
 *
 * @param string $account_name 
 * @return Array
 */

function helprace_account_context( $account_name ){
 // All keywords support by helprace	
 $keywords = array(
	array(
		"content" => "Customer Service",
		"url" => "http://helprace.com/customer-service",
	),
	array(
		"content" => "Customer Support",
		"url" => "http://helprace.com/customer-support",
	),
	array(
		"content" => "Customer Service Software",
		"url" => "http://helprace.com/customer-service-software",
	),
	array(
		"content" => "Customer Support Software",
		"url" => "http://helprace.com/customer-support-software",
	),
	array(
		"content" => "IT Help Desk Software",
		"url" => "http://helprace.com/it-help-desk-software",
	),
	array(
		"content" => "Online Help Desk Software",
		"url" => "http://helprace.com/online-help-desk-software",
	),
	array(
		"content" => "Help Desk Software",
		"url" => "http://helprace.com/help-desk-software",
	),
	array(
		"content" => "Help Desk",
		"url" => "http://helprace.com/help-desk",
	),
	array(
		"content" => "Help Desk Support",
		"url" => "http://helprace.com/help-desk-support",
	),
	array(
		"content" => "IT Help Desk",
		"url" => "http://helprace.com/it-help-desk",
	),
	array(
		"content" => "Web Help Desk",
		"url" => "http://helprace.com/web-help-desk",
	),
	array(
		"content" => "Helpdesk",
		"url" => "http://helprace.com/helpdesk",
	),
	array(
		"content" => "Helpdesk Software",
		"url" => "http://helprace.com/helpdesk-software",
	),
	array(
		"content" => "IT Helpdesk Software",
		"url" => "http://helprace.com/it-helpdesk-software",
	),
	array(
		"content" => "IT Helpdesk",
		"url" => "http://helprace.com/it-helpdesk",
	),
	array(
		"content" => "Web Helpdesk",
		"url" => "http://helprace.com/web-helpdesk",
	),
	array(
		"content" => "Helpdesk Support",
		"url" => "http://helprace.com/helpdesk-support",
	),
	array(
		"content" => "Helpdesk Ticketing System",
		"url" => "http://helprace.com/helpdesk-ticketing-system",
	),
	array(
		"content" => "Support Software",
		"url" => "http://helprace.com/support-software",
	),
	array(
		"content" => "Service Desk",
		"url" => "http://helprace.com/service-desk",
	),
	array(
		"content" => "IT Service Desk",
		"url" => "http://helprace.com/it-service-desk",
	),
	array(
		"content" => "Servicedesk",
		"url" => "http://helprace.com/servicedesk",
	),
	array(
		"content" => "Service Desk Software",
		"url" => "http://helprace.com/service-desk-software",
	),
	array(
		"content" => "Knowledgebase",
		"url" => "http://helprace.com/knowledgebase",
	),
	array(
		"content" => "Knowledge Base",
		"url" => "http://helprace.com/knowledge-base",
	),
	array(
		"content" => "Knowledgebase Software",
		"url" => "http://helprace.com/knowledgebase-software",
	),
	array(
		"content" => "Knowledge Base Software",
		"url" => "http://helprace.com/knowledge-base-software",
	),
	array(
		"content" => "Ticketing System",
		"url" => "http://helprace.com/ticketing-system",
	),
	array(
		"content" => "Support Ticket System",
		"url" => "http://helprace.com/support-ticket-system",
	),
	array(
		"content" => "Community Development",
		"url" => "http://helprace.com/community-development",
	),
	array(
		"content" => "Social Software",
		"url" => "http://helprace.com/social-software",
	),
	array(
		"content" => "Online Communities",
		"url" => "http://helprace.com/online-communities",
	),
	array(
		"content" => "Community Management",
		"url" => "http://helprace.com/community-management",
	),
	array(
		"content" => "Customer Engagement",
		"url" => "http://helprace.com/customer-engagement",
	),
	array(
		"content" => "Customer Feedback",
		"url" => "http://helprace.com/customer-feedback",
	),
);
	
	for ($i = 0; $i < strlen($account_name); $i++) {
		$hash += ord($account_name[$i]);
	}
	
	$keyword_id = $hash % 36;
    return $keywords[$keyword_id];
} 		
