<?php

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
	$ageLimit = get_option('top_opt_age_limit');
	if (!isset($omitCats)) {
		$omitCats = top_opt_OMIT_CATS;
	}
	if (!isset($ageLimit)) {
		$ageLimit = top_opt_AGE_LIMIT;
	}
	$sql = "SELECT ID
            FROM $wpdb->posts
            WHERE post_type = 'post'
                  AND post_status = 'publish'
                  AND post_date < curdate( ) - INTERVAL ".$ageLimit." DAY 
                  ";
	if ($omitCats!='') {
		$sql = $sql."AND NOT(ID IN (SELECT tr.object_id
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
		top_opt_tweet_post($oldest_post);
	}
}


//tweet for the passed random post
function top_opt_tweet_post($oldest_post)
{
	$post = get_post($oldest_post);

	$permalink = get_permalink($oldest_post);
	$shorturl = shorten_url($permalink);
	$content = stripslashes($post->post_content);
	$content = strip_tags($content);
	$content = preg_replace('/\s\s+/', ' ', $content);
	if(!is_numeric($shorturl))
	{
		$message = set_tweet_length($post->post_title. ": ". $content,$shorturl);
		$username = get_option('top_opt_twitter_username');
		$password = get_option('top_opt_twitter_password');


		$status = urlencode(stripslashes(urldecode($message)));

		if ($status) {
			$tweetUrl = 'http://www.twitter.com/statuses/update.xml';

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "$tweetUrl");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status&source=Tweet Old Post");
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

			$result = curl_exec($curl);
			$resultArray = curl_getinfo($curl);

			if ($resultArray['http_code'] == 200)
			echo 'Tweet Posted';
			else
			echo 'Could not post Tweet to Twitter right now. Try again later.';

			curl_close($curl);
		}

	}
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

	if ($shortener=="bit.ly" && isset($api_key) && isset($user)) {
		$url = "http://api.bit.ly/shorten?version=2.0.1&longUrl={$the_url}&login={$user}&apiKey={$api_key}&format=xml";
		$response = send_request($url, 'GET');
		$the_results = new SimpleXmlElement($response);
		if ($the_results->errorCode == '0') {
			$response = $the_results->results->nodeKeyVal->shortUrl;
		} else {
			$response = "";
		}
	} elseif ($shortener=="su.pr") {
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
	} else {
		$url = "http://is.gd/api.php?longurl={$the_url}";
		$response = send_request($url, 'GET');
	}
	return $response;

}



//Shrink a tweet and accompanying URL down to 140 chars.
function set_tweet_length($message, $url) {

	$message_length = strlen($message);
	$url_length = strlen($url);
	if ($message_length + $url_length > 140) {
		$shorten_message_to = 140 - $url_length;
		$shorten_message_to = $shorten_message_to - 4;
		$message = $message." ";
		$message = substr($message, 0, $shorten_message_to);
		$message = substr($message, 0, strrpos($message,' '));
		$message = $message."...";
	}
	return $message." ".$url;

}

//check time and update the last tweet time
function top_opt_update_time () {
	$last = get_option('top_opt_last_update');
	$interval = get_option('top_opt_interval');
	if (!(isset($interval) && is_numeric($interval))) {
		$interval = top_opt_INTERVAL;
	}
	$slop = get_option('top_opt_interval_slop');
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

?>