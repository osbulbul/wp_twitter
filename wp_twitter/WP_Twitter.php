<?php
/**
* WP_Twitter : All in one solution for twitter needs while building WordPress themes.
*
* @package  WP_Twitter
* @author   Coder on the Road <coderontheroad@gmail.com>
* @license  MIT License
* @version  0.1.0 (Alpha)
* @link     
*/

//This class is based on TwitterAPIExchange class
require_once('TwitterAPIExchange.php');

class WP_Twitter{

	//Use settings variable for keeping twitter tokens
	public $settings = array();

	function __construct($oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret){
		//Set the twitter tokens
		$this->settings['oauth_access_token'] = $oauth_access_token;
		$this->settings['oauth_access_token_secret'] = $oauth_access_token_secret;
		$this->settings['consumer_key'] = $consumer_key;
		$this->settings['consumer_secret'] = $consumer_secret;
	}

	function tweets($username,$num_tweets,$tweet_reset_time){
		//get and return recent tweets
		return $this->fetch_tweets($num_tweets, $username, $tweet_reset_time);
	}

	function fetch_tweets($num_tweets, $username, $tweet_reset_time){
		//get recent tweets from database
		$recent_tweets = get_option('cotr_recent_tweets');
		//check for refresh cached tweet or not?
		$this->reset_data($recent_tweets, $tweet_reset_time);

		//if recent tweets are empty this means cached tweets are expired
		if(empty($recent_tweets)){
			//get tweets from twitter api
			$tweets = $this->get_tweets($username,$num_tweets);
			$data = array();

			//take the tweet texts and put them another array
			foreach ($tweets as $tweet) {
				if($num_tweets-- === 0) break;
				$data[] = $tweet->text;
			}

			//put time for checking expire or not
			$recent_tweets = array((int)date('i', time()));
			$recent_tweets[] = $data;

			//save tweets to database
			$this->cache($recent_tweets);
		}
		return isset($recent_tweets[0][1]) ? $recent_tweets[0][1] : $recent_tweets[1];
	}

	function get_tweets($username,$num_tweets){
		//connect to twitter api and get tweets with Twitter API Exchange
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = "?screen_name=$username&count=$num_tweets";
		$requestMethod = 'GET';

		$twitter = new TwitterAPIExchange($this->settings);
		$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
		return json_decode($response);
	}

	function cache($recent_tweets){
		//add recent tweets to database
		add_option('cotr_recent_tweets', $recent_tweets);
	}

	function reset_data($recent_tweets, $tweet_reset_time){
		//if tweets expired delete the option
		if(isset($recent_tweets[0])){
			$delay = $recent_tweets[0] + (int)$tweet_reset_time;
			if($delay >= 60) $delay -= 60;
			if($delay <= (int)date('i', time())){
				delete_option('cotr_recent_tweets');
			}
		}
	}
}
