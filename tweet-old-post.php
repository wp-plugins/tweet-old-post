<?php   
#     /* 
#     Plugin Name: Tweet old post
#     Plugin URI: http://www.ajaymatharu.com/wordpress-plugin-tweet-old-posts/
#     Description: Plugin for tweeting your old posts randomly 
#     Author: Ajay Matharu 
#     Version: 3.2.6
#     Author URI: http://www.ajaymatharu.com
#     */  
 

require_once('top-admin.php');
require_once('top-core.php');


define ('top_opt_1_HOUR', 60*60);
define ('top_opt_2_HOURS', 2*top_opt_1_HOUR);
define ('top_opt_4_HOURS', 4*top_opt_1_HOUR);
define ('top_opt_8_HOURS', 8*top_opt_1_HOUR);
define ('top_opt_6_HOURS', 6*top_opt_1_HOUR); 
define ('top_opt_12_HOURS', 12*top_opt_1_HOUR); 
define ('top_opt_24_HOURS', 24*top_opt_1_HOUR); 
define ('top_opt_48_HOURS', 48*top_opt_1_HOUR); 
define ('top_opt_72_HOURS', 72*top_opt_1_HOUR); 
define ('top_opt_168_HOURS', 168*top_opt_1_HOUR); 
define ('top_opt_INTERVAL', 4);
define ('top_opt_INTERVAL_SLOP', 4);
define ('top_opt_AGE_LIMIT', 30); // 120 days
define ('top_opt_MAX_AGE_LIMIT', 60); // 120 days
define ('top_opt_OMIT_CATS', "");
define('top_opt_TWEET_PREFIX',"");
define('top_opt_ADD_DATA',"false");
define('top_opt_URL_SHORTENER',"is.gd");
define('top_opt_HASHTAGS',"");

global $top_db_version;
$top_db_version = "1.0";

   function top_admin_actions() {  
        add_menu_page("Tweet Old Post", "Tweet Old Post", 1, "TweetOldPost", "top_admin");
        
		
    }  
    
  	add_action('admin_menu', 'top_admin_actions');  
	add_action('admin_head', 'top_opt_head_admin');
 	add_action('init','top_tweet_old_post');
    
	register_activation_hook(__FILE__, "top_on_activation");
        
        
     function top_on_activation()
        {
            update_option('top_opt_interval', "4");
            update_option('top_opt_interval_slop', "4");
            update_option('top_opt_age_limit', "30");
            update_option('top_opt_max_age_limit', "60");
        }


add_filter('plugin_action_links', 'top_plugin_action_links', 10, 2);

function top_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=TweetOldPost">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

?>