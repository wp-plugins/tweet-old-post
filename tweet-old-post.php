<?php   
#     /* 
#     Plugin Name: Tweet old post
#     Plugin URI: http://www.ajaymatharu.com/wordpress-plugin-tweet-old-posts/
#     Description: Plugin for tweeting your old posts randomly 
#     Author: Ajay Matharu 
#     Version: 3.1
#     Author URI: http://www.ajaymatharu.com
#     */  
 

require_once('top-admin.php');
require_once('top-core.php');
require_once ('top-excludepost.php');
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
define ('top_opt_MAX_AGE_LIMIT', 0); // 120 days
define ('top_opt_OMIT_CATS', "");
define('top_opt_TWEET_PREFIX',"");
define('top_opt_ADD_DATA',"false");
define('top_opt_URL_SHORTENER',"is.gd");
define('top_opt_HASHTAGS',"");

   function top_admin_actions() {  
        add_menu_page("Tweet Old Post", "Tweet Old Post", 1, "TweetOldPost", "top_admin");
        add_submenu_page("TweetOldPost", __('Exclude Posts','TweetOldPost'), __('Exclude Posts','TweetOldPost'), 1, __('ExcludePosts','TweetOldPost'), 'top_exclude');
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
            update_option('top_opt_max_age_limit', "0");
        }
?>