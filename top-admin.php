<?php
require_once('tweet-old-post.php');
require_once('top-core.php');
require_once( 'Include/oauth.php' );
require_once('xml.php');
function top_admin() {
	if ( current_user_can('manage_options') ) { 
	$message = null;
	$message_updated = __("Tweet Old Post Options Updated.", 'TweetOldPost');
	$response=null;
	$save=true;
$settings = top_get_settings();
	if ( isset( $_GET['TOP_oauth'] ) ) {
		global $top_oauth;

		
		$result = $top_oauth->get_access_token( $settings['oauth_request_token'], $settings['oauth_request_token_secret'], $_GET['oauth_verifier'] );
		
                if ( $result ) {
			$settings['oauth_access_token'] = $result['oauth_token'];
			$settings['oauth_access_token_secret'] = $result['oauth_token_secret'];
			$settings['user_id'] = $result['user_id'];

			$result = $top_oauth->get_user_info( $result['user_id'] );
			if ( $result ) {
				$settings['profile_image_url'] = $result['user']['profile_image_url'];
				$settings['screen_name'] = $result['user']['screen_name'];
				if ( isset( $result['user']['location'] ) ) {
					$settings['location'] = $result['user']['location'];
				} else {
					$settings['location'] = false;
				}
			}

			top_save_settings( $settings );
echo '<script language="javascript">window.open ("'.get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=TweetOldPost","_self")</script>';
			//header( 'Location: ' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=TweetOldPost' );
			die;
		}
	} else if ( isset( $_GET['top'] ) && $_GET['top'] == 'deauthorize' ) {
		$settings = top_get_settings();
		$settings['oauth_access_token'] = '';
		$settings['oauth_access_token_secret'] = '';
		$settings['user_id'] = '';
		$settings['tweet_queue'] = array();

		top_save_settings( $settings );
echo '<script language="javascript">window.open ("'.get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=TweetOldPost","_self")</script>';
		//header( 'Location: ' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=TweetOldPost' );
		die;
	}

	if(isset($_POST['top_opt_url_shortener']))
	{
		if($_POST['top_opt_url_shortener']=="bit.ly")
		{
			
				if(!isset($_POST['top_opt_bitly_user']))
				{
					print('
			<div id="message" class="updated fade">
				<p>'.__('Please enter bit.ly username.', 'TweetOldPost').'</p>
			</div>');
					$save=false;
				}
				elseif(!isset($_POST['top_opt_bitly_key']))
				{
					print('
			<div id="message" class="updated fade">
				<p>'.__('Please enter bit.ly API Key.', 'TweetOldPost').'</p>
			</div>');
					$save=false;
				}
				else
				{
					$save=true;
				}
			
		}
		
	}

	if (isset($_POST['submit']) && $save ) {
		$message = $message_updated;
		
		if (isset($_POST['top_opt_interval'])) {
			update_option('top_opt_interval',$_POST['top_opt_interval']);
		}
		if (isset($_POST['top_opt_interval_slop'])) {
			update_option('top_opt_interval_slop',$_POST['top_opt_interval_slop']);
		}
		if (isset($_POST['top_opt_age_limit'])) {
			update_option('top_opt_age_limit',$_POST['top_opt_age_limit']);
		}
		if (isset($_POST['top_opt_max_age_limit'])) {
			update_option('top_opt_max_age_limit',$_POST['top_opt_max_age_limit']);
		}
		if (isset($_POST['top_opt_tweet_prefix'])) {
			update_option('top_opt_tweet_prefix',$_POST['top_opt_tweet_prefix']);
		}
		if (isset($_POST['top_opt_add_data'])) {
			update_option('top_opt_add_data',$_POST['top_opt_add_data']);
		}
		if (isset($_POST['post_category'])) {
			update_option('top_opt_omit_cats',implode(',',$_POST['post_category']));
		}
		else {
			update_option('top_opt_omit_cats','');
		}
		
			
        if (isset($_POST['top_opt_custom_url_option'])) {
			update_option('top_opt_custom_url_option',true);
			
		}
		else {
			
			update_option('top_opt_custom_url_option',false);
		}
		
		if (isset($_POST['top_opt_custom_url_field'])) {
			update_option('top_opt_custom_url_field',$_POST['top_opt_custom_url_field']);
		}
		else {
			
			update_option('top_opt_custom_url_field','');
		}
		
		if (isset($_POST['top_opt_use_url_shortner'])) {
			update_option('top_opt_use_url_shortner',true);
		}
		else {
			
			update_option('top_opt_use_url_shortner',false);
		}
		
        if (isset($_POST['top_opt_hashtags'])) {
			update_option('top_opt_hashtags',$_POST['top_opt_hashtags']);
		}
		else {
			update_option('top_opt_hashtags','');
		}
		
		if(isset($_POST['top_opt_url_shortener']))
		{
			update_option('top_opt_url_shortener',$_POST['top_opt_url_shortener']);
			if($_POST['top_opt_url_shortener']=="bit.ly")
			{
				if(isset($_POST['top_opt_bitly_user']))
				{
					update_option('top_opt_bitly_user',$_POST['top_opt_bitly_user']);
				}
				if(isset($_POST['top_opt_bitly_key']))
				{
					update_option('top_opt_bitly_key',$_POST['top_opt_bitly_key']);
				}
			}
			
		}
		print('
			<div id="message" class="updated fade">
				<p>'.__('Tweet Old Post Options Updated.', 'TweetOldPost').'</p>
			</div>');
	}
	elseif (isset($_POST['tweet']))
	{
		$tweet_msg = top_opt_tweet_old_post();
		print('
			<div id="message" class="updated fade">
				<p>'.__($tweet_msg, 'TweetOldPost').'</p>
			</div>');
	}
	$omitCats = get_option('top_opt_omit_cats');
	if (!isset($omitCats)) {
		$omitCats = top_opt_OMIT_CATS;
	}
	$ageLimit = get_option('top_opt_age_limit');
	if (!(isset($ageLimit) && is_numeric($ageLimit))) {
		$ageLimit = top_opt_AGE_LIMIT;
	}

	$maxAgeLimit = get_option('top_opt_max_age_limit');
	if (!(isset($maxAgeLimit) && is_numeric($maxAgeLimit))) {
		$maxAgeLimit = top_opt_MAX_AGE_LIMIT;
	}
	
	$interval = get_option('top_opt_interval');
	if (!(isset($interval) && is_numeric($interval))) {
		$interval = top_opt_INTERVAL;
	}
	$slop = get_option('top_opt_interval_slop');
	if (!(isset($slop) && is_numeric($slop))) {
		$slop = top_opt_INTERVAL_SLOP;
	}
	$tweet_prefix = get_option('top_opt_tweet_prefix');
	if(!isset($tweet_prefix)){
		$tweet_prefix = top_opt_TWEET_PREFIX;
	}
	$url_shortener=get_option('top_opt_url_shortener');
	if(!isset($url_shortener)){
		$url_shortener=top_opt_URL_SHORTENER;
	}
	
	
	$twitter_hashtags=get_option('top_opt_hashtags');
	if(!isset($twitter_hashtags)){
		$twitter_hashtags=top_opt_HASHTAGS;
	}
	
	$bitly_api=get_option('top_opt_bitly_key');
	if(!isset($bitly_api)){
		$bitly_api="";
	}
	$bitly_username=get_option('top_opt_bitly_user');
	if(!isset($bitly_username)){
		$bitly_username="";
	}
	
	$custom_url_option=get_option('top_opt_custom_url_option');
	
	if(!isset($custom_url_option)){
		$custom_url_option="";
	}
	elseif ($custom_url_option)
		$custom_url_option="checked";
	else 
		$custom_url_option="";
	
	$custom_url_field=get_option('top_opt_custom_url_field');
	if(!isset($custom_url_field)){
		$custom_url_field="";
	}
	
	$use_url_shortner=get_option('top_opt_use_url_shortner');
	if(!isset($use_url_shortner)){
		$use_url_shortner="";
	}
	elseif($use_url_shortner) 
		$use_url_shortner="checked";
	else 
		$use_url_shortner="";
	$add_data = get_option('top_opt_add_data');
	$twitter_username = get_option('top_opt_twitter_username');
	$twitter_password = get_option('top_opt_twitter_password');

	print('
			<div class="wrap">
				<h2>'.__('Tweet old post by - ', 'TweetOldPost').' <a href="http://www.ajaymatharu.com">Ajay Matharu</a></h2>
				<form id="top_opt" name="top_TweetOldPost" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=TweetOldPost" method="post">
					<input type="hidden" name="top_opt_action" value="top_opt_update_settings" />
					<fieldset class="options">
						<div class="option">
							<label for="top_opt_twitter_username">'.__('Account Login', 'TweetOldPost').':</label>

<div id="profile-box">');
					if ( !$settings["oauth_access_token"] ) {
						echo '<a href="'. top_get_auth_url() .'"><img src="http://apiwiki.twitter.com/f/1242697607/Sign-in-with-Twitter-lighter-small.png" /></a>';
					 } else {
							echo '<img class="avatar" src="'. $settings["profile_image_url"] .'" alt="" />
							<h4>'. $settings["screen_name"] .'</h4>';
							 if ( $settings["location"] ) {
							echo '<h5>'.  $settings["location"].'</h5>';
							 }
							echo '<p>

								Your account has  been authorized. <a href="'.  $_SERVER["REQUEST_URI"] .'&top=deauthorize" onclick=\'return confirm("Are you sure you want to deauthorize your Twitter account?");\'>Click to deauthorize</a>.<br />

							</p>

							<div class="retweet-clear"></div>
					'; }
					print('</div>
						</div>
						
						<div class="option">
							<label for="top_opt_tweet_prefix">'.__('Tweet Prefix', 'TweetOldPost').':</label>
							<input type="text" size="25" name="top_opt_tweet_prefix" id="top_opt_tweet_prefix" value="'.$tweet_prefix.'" autocomplete="off" />
							<b>If set, it will show as: "{tweet prefix}: {post title}... {url}</b>
						</div>
						<div class="option">
							<label for="top_opt_add_data">'.__('Add post data to tweet', 'TweetOldPost').':</label>
							<select id="top_opt_add_data" name="top_opt_add_data" style="width:100px;">
								<option value="false" '.top_opt_optionselected("false",$add_data).'>'.__(' No ', 'TweetOldPost').'</option>
								<option value="true" '.top_opt_optionselected("true",$add_data).'>'.__(' Yes ', 'TweetOldPost').'</option>
							</select>
							<b>If set, it will show as: "{tweet prefix}: {post title}- {content}... {url}</b>
						</div>
						
						<div class="option">
							<label for="top_opt_custom_url_option">'.__('Fetch URL from custom field', 'TweetOldPost').':</label>
							<input onchange="return showCustomField();" type="checkbox" name="top_opt_custom_url_option" '.$custom_url_option.' id="top_opt_custom_url_option" />
							<b>If checked URL will be fetched from custom field. If not plugin will generate shortened URL from post link.</b>
						</div>
						
						
						
						<div id="customurl" style="display:none;">
						<div class="option">
							<label for="top_opt_custom_url_field">'.__('Custom field name to fetch URL to be tweeted with post', 'TweetOldPost').':</label>
							<input type="text" size="25" name="top_opt_custom_url_field" id="top_opt_custom_url_field" value="'.$custom_url_field.'" autocomplete="off" />
							<b>If set this will fetch the URL from specified custom field</b>
						</div>
						
						</div>
						
						<div class="option">
							<label for="top_opt_use_url_shortner">'.__('Use URL shortner?', 'TweetOldPost').':</label>
							<input onchange="return showshortener()" type="checkbox" name="top_opt_use_url_shortner" id="top_opt_use_url_shortner" '.$use_url_shortner.' />
							
						</div>
						
						<div  id="urlshortener">
						<div class="option">
							<label for="top_opt_url_shortener">'.__('URL Shortener Service', 'TweetOldPost').':</label>
							<select name="top_opt_url_shortener" id="top_opt_url_shortener" onchange="javascript:showURLAPI()" style="width:100px;">
									<option value="is.gd" '.top_opt_optionselected('is.gd',$url_shortener).'>'.__('is.gd', 'TweetOldPost').'</option>
									<option value="su.pr" '.top_opt_optionselected('su.pr',$url_shortener).'>'.__('su.pr', 'TweetOldPost').'</option>
									<option value="bit.ly" '.top_opt_optionselected('bit.ly',$url_shortener).'>'.__('bit.ly', 'TweetOldPost').'</option>
									<option value="tr.im" '.top_opt_optionselected('tr.im',$url_shortener).'>'.__('tr.im', 'TweetOldPost').'</option>
									<option value="3.ly" '.top_opt_optionselected('3.ly',$url_shortener).'>'.__('3.ly', 'TweetOldPost').'</option>
									<option value="u.nu" '.top_opt_optionselected('u.nu',$url_shortener).'>'.__('u.nu', 'TweetOldPost').'</option>
									<option value="1click.at" '.top_opt_optionselected('1click.at',$url_shortener).'>'.__('1click.at', 'TweetOldPost').'</option>
									<option value="tinyurl" '.top_opt_optionselected('tinyurl',$url_shortener).'>'.__('tinyurl', 'TweetOldPost').'</option>
							</select>
						</div>
						<div id="showDetail" style="display:none">
							<div class="option">
								<label for="top_opt_bitly_user">'.__('bit.ly Username', 'TweetOldPost').':</label>
								<input type="text" size="25" name="top_opt_bitly_user" id="top_opt_bitly_user" value="'.$bitly_username.'" autocomplete="off" />
							</div>
							
							<div class="option">
								<label for="top_opt_bitly_key">'.__('bit.ly API Key', 'TweetOldPost').':</label>
								<input type="text" size="25" name="top_opt_bitly_key" id="top_opt_bitly_key" value="'.$bitly_api.'" autocomplete="off" />
							</div>
						</div>
					</div>
					
						
						<div class="option">
							<label for="top_opt_hashtags">'.__('Default #hashtags for your tweets', 'TweetOldPost').':</label>
							<input type="text" size="25" name="top_opt_hashtags" id="top_opt_hashtags" value="'.$twitter_hashtags.'" autocomplete="off" />
							<b>Include #, like #thoughts</b>
						</div>
						
						<div class="option">
							<label for="top_opt_interval">'.__('Minimum interval between tweets: ', 'TweetOldPost').'</label>
							<input type="text" id="top_opt_interval" maxlength="5" value="' . $interval .'" name="top_opt_interval" /> Hour / Hours
                                                       
						</div>
						<div class="option">
							<label for="top_opt_interval_slop">'.__('Random Interval (added to minimum interval): ', 'TweetOldPost').'</label>
							<input type="text" id="top_opt_interval_slop" maxlength="5" value="' . $slop .'" name="top_opt_interval_slop" /> Hour / Hours
                                                            
						</div>
						<div class="option">
							<label for="top_opt_age_limit">'.__('Minimum age of post to be eligible for tweet: ', 'TweetOldPost').'</label>
							<input type="text" id="top_opt_age_limit" maxlength="5" value="' . $ageLimit .'" name="top_opt_age_limit" /> Day / Days
							<b> (enter 0 for today)</b>
                                                           
						</div>
						
						<div class="option">
							<label for="top_opt_max_age_limit">'.__('Maximum age of post to be eligible for tweet: ', 'TweetOldPost').'</label>
                                                        <input type="text" id="top_opt_max_age_limit" maxlength="5" value="' . $maxAgeLimit .'" name="top_opt_max_age_limit" /> Day / Days
                                                       <b>(If you dont want to use this option enter 0 or leave blank)</b><br/>
							<b>If set, it will fetch posts which are "NOT" older than specified day / days</b>
						</div>
						
				    	<div class="option category">
				    	<div style="float:left">
						    	<label class="catlabel">'.__('Categories to Omit from tweets: ', 'TweetOldPost').'</label> </div>
						    	<div style="float:left">
						    		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
								');
	wp_category_checklist(0, 0, explode(',',$omitCats));
	print('				    		</ul>
								</div>
								</div>
					</fieldset>
					<p class="submit">
						<input type="submit" name="submit" onclick="javascript:return validate()" value="'.__('Update Tweet Old Post Options', 'TweetOldPost').'" />
						<input type="submit" name="tweet" value="'.__('Tweet Now', 'TweetOldPost').'" />
					</p>
						
				</form><script language="javascript" type="text/javascript">
function showURLAPI()
{
	var urlShortener=document.getElementById("top_opt_url_shortener").value;
	if(urlShortener=="bit.ly")
	{
		document.getElementById("showDetail").style.display="block";
		
	}
	else
	{
		document.getElementById("showDetail").style.display="none";
		
	}
	
}

function validate()
{

	if(document.getElementById("showDetail").style.display=="block" && document.getElementById("top_opt_url_shortener").value=="bit.ly")
	{
		if(trim(document.getElementById("top_opt_bitly_user").value)=="")
		{
			alert("Please enter bit.ly username.");
			document.getElementById("top_opt_bitly_user").focus();
			return false;
		}

		if(trim(document.getElementById("top_opt_bitly_key").value)=="")
		{
			alert("Please enter bit.ly API key.");
			document.getElementById("top_opt_bitly_key").focus();
			return false;
		}
	}
 if(trim(document.getElementById("top_opt_interval").value) != "" && !isNumber(trim(document.getElementById("top_opt_interval").value)))
        {
            alert("Enter only numeric in Minimum interval between tweet");
		document.getElementById("top_opt_interval").focus();
		return false;
        }
         if(trim(document.getElementById("top_opt_interval_slop").value) != "" && !isNumber(trim(document.getElementById("top_opt_interval_slop").value)))
        {
            alert("Enter only numeric in Random interval");
		document.getElementById("top_opt_interval_slop").focus();
		return false;
        }
        if(trim(document.getElementById("top_opt_age_limit").value) != "" && !isNumber(trim(document.getElementById("top_opt_age_limit").value)))
        {
            alert("Enter only numeric in Minimum age of post");
		document.getElementById("top_opt_age_limit").focus();
		return false;
        }
 if(trim(document.getElementById("top_opt_max_age_limit").value) != "" && !isNumber(trim(document.getElementById("top_opt_max_age_limit").value)))
        {
            alert("Enter only numeric in Maximum age of post");
		document.getElementById("top_opt_max_age_limit").focus();
		return false;
        }
	if(trim(document.getElementById("top_opt_max_age_limit").value) != "")
	{
	if(eval(document.getElementById("top_opt_age_limit").value) > eval(document.getElementById("top_opt_max_age_limit").value))
	{
		alert("Post max age limit cannot be less than Post min age iimit");
		document.getElementById("top_opt_age_limit").focus();
		return false;
	}
	}
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function showCustomField()
{
	if(document.getElementById("top_opt_custom_url_option").checked)
	{
		document.getElementById("customurl").style.display="block";
	}
	else
	{
		document.getElementById("customurl").style.display="none";
	}
}

function isNumber(val)
{
    if(isNaN(val)){
        return false;
    }
    else{
        return true;
    }
}

function showshortener()
{
						

	if((document.getElementById("top_opt_use_url_shortner").checked))
		{
			document.getElementById("urlshortener").style.display="block";
		}
		else
		{
			document.getElementById("urlshortener").style.display="none";
		}
}
showURLAPI();
showshortener();
showCustomField();
</script>' );

}
else 
{
	print('
			<div id="message" class="updated fade">
				<p>'.__('You do not have enough permission to set the option. Please contact your admin.', 'TweetOldPost').'</p>
			</div>');
}
}

function top_opt_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}

function top_opt_head_admin()
{
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/top-admin.php'),array('/',''),__FILE__)));
	$stylesheet = $home.'/wp-content/plugins' . $base . '/css/tweet-old-post.css';
	echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}



?>