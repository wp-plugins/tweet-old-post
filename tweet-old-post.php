<?php   
#     /* 
#     Plugin Name: Tweet old post
#     Plugin URI: http://www.ajaymatharu.com/wordpress-plugin-tweet-old-posts/
#     Description: Plugin for tweeting your old posts randomly 
#     Author: Ajay Matharu 
#     Version: 1.7
#     Author URI: http://www.ajaymatharu.com
#     */  
 

require_once('top-admin.php');
require_once('top-core.php');

define ('top_opt_1_HOUR', 60*60); 
define ('top_opt_4_HOURS', 4*top_opt_1_HOUR); 
define ('top_opt_6_HOURS', 6*top_opt_1_HOUR); 
define ('top_opt_12_HOURS', 12*top_opt_1_HOUR); 
define ('top_opt_24_HOURS', 24*top_opt_1_HOUR); 
define ('top_opt_48_HOURS', 48*top_opt_1_HOUR); 
define ('top_opt_72_HOURS', 72*top_opt_1_HOUR); 
define ('top_opt_168_HOURS', 168*top_opt_1_HOUR); 
define ('top_opt_INTERVAL', top_opt_12_HOURS); 
define ('top_opt_INTERVAL_SLOP', top_opt_4_HOURS); 
define ('top_opt_AGE_LIMIT', 30); // 120 days
define ('top_opt_MAX_AGE_LIMIT', "None"); // 120 days
define ('top_opt_OMIT_CATS', "");
define('top_opt_TWEET_PREFIX',"");
define('top_opt_ADD_DATA',"false");
define('top_opt_URL_SHORTENER',"is.gd");
define('top_opt_HASHTAGS',"");

   function top_admin_actions() {  
        add_options_page("Tweet Old Post", "Tweet Old Post", 1, "TweetOldPost", "top_admin");  
    }  
    
  	add_action('admin_menu', 'top_admin_actions');  
	add_action('admin_head', 'top_opt_head_admin');
 	add_action('init','top_tweet_old_post')

?>