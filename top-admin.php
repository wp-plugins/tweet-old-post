<?php

require_once('tweet-old-post.php');
require_once('top-core.php');


function top_admin() {
	$message = null;
	$message_updated = __("Tweet Old Post Options Updated.", 'TweetOldPost');
	$response=null;
	$save=null;

	if(isset($_POST['top_opt_twitter_password']) && isset($_POST['top_opt_twitter_username']))
	{
		$response=verify_credentials($_POST['top_opt_twitter_username'],$_POST['top_opt_twitter_password']);
		if($response != 200 && $response == 401)
		{
			$message = __("Incorrect Twitter Username & Password. Please verify your credentials.", 'TweetOldPost');
			print('
			<div id="message" class="updated fade">
				<p>'.__('Incorrect Twitter Username & Password. Please verify your credentials.', 'TweetOldPost').'</p>
			</div>');
			$save=false;
		}
		else
		$save=true;
	}

	if(isset($_POST['top_opt_url_shortener']))
	{
		if($_POST['top_opt_url_shortener']=="bit.ly")
		{
			if($save)
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
	}

	if (isset($_POST['submit']) && $save ) {
		$message = $message_updated;
		if (isset($_POST['top_opt_twitter_username'])) {
			update_option('top_opt_twitter_username',$_POST['top_opt_twitter_username']);
		}
		if (isset($_POST['top_opt_twitter_password'])) {
			update_option('top_opt_twitter_password',$_POST['top_opt_twitter_password']);
		}
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
		top_opt_tweet_old_post();
		print('
			<div id="message" class="updated fade">
				<p>'.__('Tweet posted successfully.', 'TweetOldPost').'</p>
			</div>');
	}
	$omitCats = get_option('top_opt_omit_cats');
	if (!isset($omitCats)) {
		$omitCats = top_opt_OMIT_CATS;
	}
	$ageLimit = get_option('top_opt_age_limit');
	if (!isset($ageLimit)) {
		$ageLimit = top_opt_AGE_LIMIT;
	}

	$maxAgeLimit = get_option('top_opt_max_age_limit');
	if (!isset($maxAgeLimit)) {
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
							<label for="top_opt_twitter_username">'.__('Twitter Username', 'TweetOldPost').':</label>
							<input type="text" size="25" name="top_opt_twitter_username" id="top_opt_twitter_username" value="'.$twitter_username.'" autocomplete="off" />
						</div>
						<div class="option">
							<label for="top_opt_twitter_password">'.__('Twitter Password', 'TweetOldPost').':</label>
							<input type="password" size="25" name="top_opt_twitter_password" id="top_opt_twitter_password" value="'.$twitter_password.'" autocomplete="off" />
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
							<label for="top_opt_url_shortener">'.__('URL Shortener Service', 'TweetOldPost').':</label>
							<select name="top_opt_url_shortener" id="top_opt_url_shortener" onchange="javascript:showURLAPI()" style="width:100px;">
									<option value="is.gd" '.top_opt_optionselected('is.gd',$url_shortener).'>'.__('is.gd', 'TweetOldPost').'</option>
									<option value="su.pr" '.top_opt_optionselected('su.pr',$url_shortener).'>'.__('su.pr', 'TweetOldPost').'</option>
									<option value="bit.ly" '.top_opt_optionselected('bit.ly',$url_shortener).'>'.__('bit.ly', 'TweetOldPost').'</option>
									<option value="tr.im" '.top_opt_optionselected('tr.im',$url_shortener).'>'.__('tr.im', 'TweetOldPost').'</option>
									<option value="3.ly" '.top_opt_optionselected('3.ly',$url_shortener).'>'.__('3.ly', 'TweetOldPost').'</option>
									<option value="u.nu" '.top_opt_optionselected('u.nu',$url_shortener).'>'.__('u.nu', 'TweetOldPost').'</option>
									
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
						
						<div class="option">
							<label for="top_opt_hashtags">'.__('Default #hashtags for your tweets', 'TweetOldPost').':</label>
							<input type="text" size="25" name="top_opt_hashtags" id="top_opt_hashtags" value="'.$twitter_hashtags.'" autocomplete="off" />
							<b>Include #, like #thoughts</b>
						</div>
						
						<div class="option">
							<label for="top_opt_interval">'.__('Minimum interval between tweets: ', 'TweetOldPost').'</label>
							<select name="top_opt_interval" id="top_opt_interval">
									<option value="'.top_opt_1_HOUR.'" '.top_opt_optionselected(top_opt_1_HOUR,$interval).'>'.__('1 Hour', 'TweetOldPost').'</option>
									<option value="'.top_opt_4_HOURS.'" '.top_opt_optionselected(top_opt_4_HOURS,$interval).'>'.__('4 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_6_HOURS.'" '.top_opt_optionselected(top_opt_6_HOURS,$interval).'>'.__('6 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_12_HOURS.'" '.top_opt_optionselected(top_opt_12_HOURS,$interval).'>'.__('12 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_24_HOURS.'" '.top_opt_optionselected(top_opt_24_HOURS,$interval).'>'.__('24 Hours (1)', 'TweetOldPost').'</option>
									<option value="'.top_opt_48_HOURS.'" '.top_opt_optionselected(top_opt_48_HOURS,$interval).'>'.__('48 Hours (2 days)', 'TweetOldPost').'</option>
									<option value="'.top_opt_72_HOURS.'" '.top_opt_optionselected(top_opt_72_HOURS,$interval).'>'.__('72 Hours (3 days)', 'TweetOldPost').'</option>
									<option value="'.top_opt_168_HOURS.'" '.top_opt_optionselected(top_opt_168_HOURS,$interval).'>'.__('168 Hours (7 days)', 'TweetOldPost').'</option>
							</select>
						</div>
						<div class="option">
							<label for="top_opt_interval_slop">'.__('Random Interval (added to minimum interval): ', 'TweetOldPost').'</label>
							<select name="top_opt_interval_slop" id="top_opt_interval_slop">
									<option value="'.top_opt_1_HOUR.'" '.top_opt_optionselected(top_opt_1_HOUR,$slop).'>'.__('Upto 1 Hour', 'TweetOldPost').'</option>
									<option value="'.top_opt_4_HOURS.'" '.top_opt_optionselected(top_opt_4_HOURS,$slop).'>'.__('Upto 4 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_6_HOURS.'" '.top_opt_optionselected(top_opt_6_HOURS,$slop).'>'.__('Upto 6 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_12_HOURS.'" '.top_opt_optionselected(top_opt_12_HOURS,$slop).'>'.__('Upto 12 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_24_HOURS.'" '.top_opt_optionselected(top_opt_24_HOURS,$slop).'>'.__('Upto 24 Hours (1)', 'TweetOldPost').'</option>
							</select>
						</div>
						<div class="option">
							<label for="top_opt_age_limit">'.__('Minimum age of post to be eligible for tweet: ', 'TweetOldPost').'</label>
							<select name="top_opt_age_limit" id="top_opt_age_limit">
									<option value="7" '.top_opt_optionselected('7',$ageLimit).'>'.__('7 Days', 'TweetOldPost').'</option>
									<option value="15" '.top_opt_optionselected('15',$ageLimit).'>'.__('15 Days', 'TweetOldPost').'</option>
									<option value="30" '.top_opt_optionselected('30',$ageLimit).'>'.__('30 Days', 'TweetOldPost').'</option>
									<option value="60" '.top_opt_optionselected('60',$ageLimit).'>'.__('60 Days', 'TweetOldPost').'</option>
									<option value="90" '.top_opt_optionselected('90',$ageLimit).'>'.__('90 Days', 'TweetOldPost').'</option>
									<option value="120" '.top_opt_optionselected('120',$ageLimit).'>'.__('120 Days', 'TweetOldPost').'</option>
									<option value="240" '.top_opt_optionselected('240',$ageLimit).'>'.__('240 Days', 'TweetOldPost').'</option>
									<option value="365" '.top_opt_optionselected('365',$ageLimit).'>'.__('365 Days', 'TweetOldPost').'</option>
							</select>
						</div>
						
						<div class="option">
							<label for="top_opt_max_age_limit">'.__('Maximum age of post to be eligible for tweet: ', 'TweetOldPost').'</label>
							<select name="top_opt_max_age_limit" id="top_opt_max_age_limit">
									<option value="None" '.top_opt_optionselected('None',$maxAgeLimit).'>'.__('None', 'TweetOldPost').'</option>
									<option value="15" '.top_opt_optionselected('15',$maxAgeLimit).'>'.__('15 Days', 'TweetOldPost').'</option>
									<option value="30" '.top_opt_optionselected('30',$maxAgeLimit).'>'.__('30 Days', 'TweetOldPost').'</option>
									<option value="60" '.top_opt_optionselected('60',$maxAgeLimit).'>'.__('60 Days', 'TweetOldPost').'</option>
									<option value="90" '.top_opt_optionselected('90',$maxAgeLimit).'>'.__('90 Days', 'TweetOldPost').'</option>
									<option value="120" '.top_opt_optionselected('120',$maxAgeLimit).'>'.__('120 Days', 'TweetOldPost').'</option>
									<option value="240" '.top_opt_optionselected('240',$maxAgeLimit).'>'.__('240 Days', 'TweetOldPost').'</option>
									<option value="365" '.top_opt_optionselected('365',$maxAgeLimit).'>'.__('365 Days', 'TweetOldPost').'</option>
							</select>
							<b>If set, it will not fetch posts which are older than specified day.</b>
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
	if(eval(document.getElementById("top_opt_age_limit").value) > eval(document.getElementById("top_opt_max_age_limit").value))
	{
		alert("Post max age limit cannot be less than Post min age iimit");
		document.getElementById("top_opt_age_limit").focus();
		return false;
	}
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

showURLAPI();
</script>' );

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


//Verify a user's credentials
function verify_credentials($auth_user, $auth_pass) {

	$url = "http://twitter.com/account/verify_credentials.xml";
	$response = send_request($url, 'GET', '', $auth_user, $auth_pass);
	if(is_numeric($response))
	{
		return $response;
	}
	else {
		$xml = new SimpleXmlElement($response);
		return $xml;}

}
?>
