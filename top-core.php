<?php
require_once( 'Include/oauth.php' );
global $top_oauth;
$top_oauth = new TOPOAuth;

function top_tweet_old_post()
{
	//check last tweet time against set interval and span
	if (top_opt_update_time()) {
		update_option('top_opt_last_update', time());
		top_opt_tweet_old_post();
	}
}

//get random post and tweet
function top_opt_tweet_old_post()
{
	global $wpdb;
	$omitCats = get_option('top_opt_omit_cats');
        $maxAgeLimit = get_option('top_opt_max_age_limit');
        $ageLimit = get_option('top_opt_age_limit');
	if (!(isset($ageLimit) && is_numeric($ageLimit))) {
		$ageLimit = top_opt_AGE_LIMIT;
	}

	if (!(isset($maxAgeLimit) && is_numeric($maxAgeLimit))) {
		$maxAgeLimit = top_opt_MAX_AGE_LIMIT;
	}
	if (!isset($omitCats)) {
		$omitCats = top_opt_OMIT_CATS;
	}
	
	$sql = "SELECT ID
            FROM $wpdb->posts
            WHERE post_type = 'post'
                  AND post_status = 'publish'
                  AND post_date <= curdate( ) - INTERVAL ".$ageLimit. " day";

	if($maxAgeLimit != 0)
	{
		$sql = $sql." AND post_date >= curdate( ) - INTERVAL ".$maxAgeLimit." day";
	}

	if ($omitCats!='') {
		$sql = $sql." AND NOT(ID IN (SELECT tr.object_id
                                    FROM $wpdb->terms  t 
                                          inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category' 
                                          inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id 
                                    WHERE t.term_id IN (".$omitCats.")))";
	}
	$sql = $sql."
            ORDER BY RAND() 
            LIMIT 1 ";
        
	$oldest_post = $wpdb->get_var($sql);
	if (isset($oldest_post)) {
		return top_opt_tweet_post($oldest_post);
	}
}


//tweet for the passed random post
function top_opt_tweet_post($oldest_post)
{
	$post = get_post($oldest_post);
	$content=null;
	$permalink = get_permalink($oldest_post);
	$add_data = get_option("top_opt_add_data");
	$twitter_hashtags = get_option('top_opt_hashtags');
	$url_shortener=get_option('top_opt_url_shortener');
	$to_short_url=true;

	$custom_url_option=get_option('top_opt_custom_url_option');
	$to_short_url = get_option('top_opt_use_url_shortner');
	
	if($custom_url_option)
	{
		$custom_url_field = get_option('top_opt_custom_url_field');
		if(trim($custom_url_field) != "")
		{
			$permalink = trim(get_post_meta($post->ID, $custom_url_field, true));
		}
	}

	if($to_short_url)
	{
		
		if($url_shortener=="bit.ly")
		{
			$bitly_key=get_option('top_opt_bitly_key');
			$bitly_user=get_option('top_opt_bitly_user');
			$shorturl=shorten_url($permalink,$url_shortener,$bitly_key,$bitly_user);
		}
		else
		{
			$shorturl = shorten_url($permalink,$url_shortener);
		}
	}
	else 
	{
		$shorturl = $permalink;
	}
	$prefix=get_option('top_opt_tweet_prefix');

	if($add_data == "true")
	{
		$content = stripslashes($post->post_content);
		$content = strip_tags($content);
		$content = preg_replace('/\s\s+/', ' ', $content);
		$content = " - ".$content;
	}
	else {
		$content="";
	}


	if(!is_numeric($shorturl) && (strncmp($shorturl, "http", strlen("http")) == 0))
	{
		if($prefix)
		{
			$message = $prefix.": ".$post->post_title;
		}
		else {
			$message = $post->post_title;
		}

		$message = set_tweet_length($message.$content,$shorturl,$twitter_hashtags);

		$username = get_option('top_opt_twitter_username');
		$password = get_option('top_opt_twitter_password');


		$status = urlencode(stripslashes(urldecode($message)));

		if ($status) {
			/*$tweetUrl = 'http://www.twitter.com/statuses/update.xml';

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "$tweetUrl");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status&source=TweetOldPost");
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

			$result = curl_exec($curl);
			$resultArray = curl_getinfo($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);*/
                   $poststatus= top_update_status($message);
			
			if($poststatus == true)
			return "Whoopie!!! Tweet Posted Successfully";
			else
			return "OOPS!!! there seems to be some problem while tweeting. Tweet request returned error code " . $httpcode . ".";
		}
		return "OOPS!!! there seems to be some problem while tweeting. Try again. If problem is persistent mail the problem at ajay@ajaymatharu.com";
	}
	return "OOPS!!! problem with your URL shortning service. Some signs of error " . $shorturl . ".";
}

//send request to passed url and return the response
function send_request($url, $method='GET', $data='', $auth_user='', $auth_pass='') {
	$ch = curl_init($url);
	if (strtoupper($method)=="POST") {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off'){
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($auth_user != '' && $auth_pass != '') {
		curl_setopt($ch, CURLOPT_USERPWD, "{$auth_user}:{$auth_pass}");
	}
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpcode != 200) {
		return $httpcode;
	}

	return $response;

}


//Shorten long URLs with is.gd or bit.ly.
function shorten_url($the_url, $shortener='is.gd', $api_key='', $user='') {

	if (($shortener=="bit.ly") && isset($api_key) && isset($user)) {
		$url = "http://api.bit.ly/shorten?version=2.0.1&longUrl={$the_url}&login={$user}&apiKey={$api_key}&format=xml";
		$response = send_request($url, 'GET');
		
		$the_results = new SimpleXmlElement($response);
		if ($the_results->errorCode == '0') {
			$response = $the_results->results->nodeKeyVal->shortUrl;
		} else {
			$response = "";
		}
		
	}elseif ($shortener=="su.pr") {
		$url = "http://su.pr/api/simpleshorten?url={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="tr.im") {
		$url = "http://api.tr.im/api/trim_simple?url={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="3.ly") {
		$url = "http://3.ly/?api=em5893833&u={$the_url}";
		$response = send_request($url, 'GET');
	} elseif ($shortener=="tinyurl") {
		$url = "http://tinyurl.com/api-create.php?url={$the_url}";
		$response = send_request($url, 'GET');
	}elseif ($shortener=="u.nu") {
		$url = "http://u.nu/unu-api-simple?url={$the_url}";
		$response = send_request($url, 'GET');
	}elseif ($shortener=="1click.at") {
		$url = "http://1click.at/api.php?action=shorturl&url={$the_url}&format=simple";
		$response = send_request($url, 'GET');
	} else {
		$url = "http://is.gd/api.php?longurl={$the_url}";
		$response = send_request($url, 'GET');
	}
	
	return $response;

}

//Shrink a tweet and accompanying URL down to 140 chars.
function set_tweet_length($message, $url, $twitter_hashtags="") {

	$message_length = strlen($message);
	$url_length = strlen($url);
	$hashtags_length = strlen($twitter_hashtags);
	if ($message_length + $url_length + $hashtags_length > 140) {
		$shorten_message_to = 140 - $url_length - $hashtags_length;
		$shorten_message_to = $shorten_message_to - 4;
		//$message = $message." ";
		$message = substr($message, 0, $shorten_message_to);
		$message = substr($message, 0, strrpos($message,' '));
		$message = $message."...";
	}
	return $message." ".$url." ".$twitter_hashtags;

}

//check time and update the last tweet time
function top_opt_update_time () {
	$last = get_option('top_opt_last_update');
        $interval = get_option('top_opt_interval');
        $slop = get_option('top_opt_interval_slop');
        if(is_numeric($interval)){$interval = $interval * 60 * 60;}
        if(is_numeric($slop)){$slop = $slop * 60 * 60;}

        if (!(isset($interval) && is_numeric($interval))) {
		$interval = top_opt_INTERVAL;
	}

        if (!(isset($slop) && is_numeric($slop))) {
		$slop = top_opt_INTERVAL_SLOP;
	}
	if (false === $last) {
		$ret = 1;
	} else if (is_numeric($last)) {
		$ret = ( (time() - $last) > ($interval+rand(0,$slop)));
	}
	return $ret;
}

function top_get_auth_url() {
	global $top_oauth;
	$settings = top_get_settings();

	$token = $top_oauth->get_request_token();
	if ( $token ) {
		$settings['oauth_request_token'] = $token['oauth_token'];
		$settings['oauth_request_token_secret'] = $token['oauth_token_secret'];

		top_save_settings( $settings );

		return $top_oauth->get_auth_url( $token['oauth_token'] );
	}

}


function top_update_status( $new_status ) {
	global $top_oauth;
	$settings = top_get_settings();

	if ( isset( $settings['oauth_access_token'] ) && isset( $settings['oauth_access_token_secret'] ) ) {
		return $top_oauth->update_status( $settings['oauth_access_token'], $settings['oauth_access_token_secret'], $new_status );
	}

	return false;
}

function top_has_tokens() {
	$settings = top_get_settings();

	return ( $settings[ 'oauth_access_token' ] && $settings['oauth_access_token_secret'] );
}

function top_is_valid() {
	return twit_has_tokens();
}

function top_do_tweet( $post_id ) {
	$settings = top_get_settings();

	$message = top_get_message( $post_id );

	// If we have a valid message, Tweet it
	// this will fail if the Tiny URL service is done
	if ( $message ) {
		// If we successfully posted this to Twitter, then we can remove it from the queue eventually
		if ( twit_update_status( $message ) ) {
			return true;
		}
	}

	return false;
}
function top_get_settings() {
	global $top_defaults;

	$settings = $top_defaults;

	$wordpress_settings = get_option( 'top_settings' );
	if ( $wordpress_settings ) {
		foreach( $wordpress_settings as $key => $value ) {
			$settings[ $key ] = $value;
		}
	}

	return $settings;
}

function top_save_settings( $settings ) {
	update_option( 'top_settings', $settings );
}
?>