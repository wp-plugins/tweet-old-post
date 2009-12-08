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
							<select id="top_opt_add_data" name="top_opt_add_data">
								<option value="false" '.top_opt_optionselected("false",$add_data).'>'.__(' No ', 'TweetOldPost').'</option>
								<option value="true" '.top_opt_optionselected("true",$add_data).'>'.__(' Yes ', 'TweetOldPost').'</option>
							</select>
							<b>If set, it will show as: "{tweet prefix}: {post title}- {content}... {url}</b>
						</div>
						
						<div class="option">
							<label for="top_opt_interval">'.__('Minimum interval between tweets: ', 'TweetOldPost').'</label>
							<select name="top_opt_interval" id="top_opt_interval">
									<option value="'.top_opt_1_HOUR.'" '.top_opt_optionselected(top_opt_1_HOUR,$interval).'>'.__('1 Hour', 'TweetOldPost').'</option>
									<option value="'.top_opt_4_HOURS.'" '.top_opt_optionselected(top_opt_4_HOURS,$interval).'>'.__('4 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_6_HOURS.'" '.top_opt_optionselected(top_opt_6_HOURS,$interval).'>'.__('6 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_12_HOURS.'" '.top_opt_optionselected(top_opt_12_HOURS,$interval).'>'.__('12 Hours', 'TweetOldPost').'</option>
									<option value="'.top_opt_24_HOURS.'" '.top_opt_optionselected(top_opt_24_HOURS,$interval).'>'.__('24 Hours (1 day)', 'TweetOldPost').'</option>
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
									<option value="'.top_opt_24_HOURS.'" '.top_opt_optionselected(top_opt_24_HOURS,$slop).'>'.__('Upto 24 Hours (1 day)', 'TweetOldPost').'</option>
							</select>
						</div>
						<div class="option">
							<label for="top_opt_age_limit">'.__('Minimum age of post to be eligible for tweet: ', 'TweetOldPost').'</label>
							<select name="top_opt_age_limit" id="top_opt_age_limit">
									<option value="7" '.top_opt_optionselected(7,$ageLimit).'>'.__('7 Days', 'TweetOldPost').'</option>
									<option value="15" '.top_opt_optionselected(15,$ageLimit).'>'.__('15 Days', 'TweetOldPost').'</option>
									<option value="30" '.top_opt_optionselected(30,$ageLimit).'>'.__('30 Days', 'TweetOldPost').'</option>
									<option value="60" '.top_opt_optionselected(60,$ageLimit).'>'.__('60 Days', 'TweetOldPost').'</option>
									<option value="90" '.top_opt_optionselected(90,$ageLimit).'>'.__('90 Days', 'TweetOldPost').'</option>
									<option value="120" '.top_opt_optionselected(120,$ageLimit).'>'.__('120 Days', 'TweetOldPost').'</option>
									<option value="240" '.top_opt_optionselected(240,$ageLimit).'>'.__('240 Days', 'TweetOldPost').'</option>
									<option value="365" '.top_opt_optionselected(365,$ageLimit).'>'.__('365 Days', 'TweetOldPost').'</option>
							</select>
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
						<input type="submit" name="submit" value="'.__('Update Tweet Old Post Options', 'TweetOldPost').'" />
						<input type="submit" name="tweet" value="'.__('Tweet Now', 'TweetOldPost').'" />
					</p>
						
				</form>' );

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