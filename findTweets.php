<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

spl_autoload_register(function ($class) {
	if(file_exists("src/class.{$class}.php")){
		include_once("src/class.{$class}.php");
	}
});

include "keys/keys.php";

$usernames = $_POST['usernames'];
$numTweets = $_POST['numTweets'];
$searchTerm = $_POST['searchTerm'];
$latLong = $_POST['latLong'];
$ignoreRetweets = $_POST['ignoreRetweets'];

$error = '';
$tweets = '';

try {

	// instantiate a TwitterOAuth object with the Consumer Key / Secret
	$twitterOAuth = new TwitterOAuth($twitterConsumerKey,
                        $twitterConsumerSecret);

	// instantiate twitter search object
	$twitter = new twitterSearch($twitterConsumerKey,
		$twitterConsumerSecret);

	// set the curl member variable (dependency injection)
	$curl = new curl();
	$twitter->setCurl($curl);

	// perform authentication
	$authArray = $twitter->authenticateApp($twitterOAuth);

	// since TwitterOAuth has no method to just set the bearer member
	// variable for app-level auth, we must re-instantiate and pass to
	// twitterSearch class
	$twitterOAuth = new TwitterOAuth($authArray['consumerKey'],
		$authArray['consumerSecret'], $authArray['oauthToken'],
		$authArray['oauthTokenSecret']);
	$twitter->setTwitter($twitterOAuth);

	// perform the search
	$tweets = $twitter->search($usernames, $numTweets, $searchTerm,
		$latLong, $ignoreRetweets);

} catch (Exception $exc) {

	$error = $exc->getMessage();
}

$response = array('tweets' => $tweets);

if ($error) {

	$response['error'] = $error;
}

echo json_encode($response);

?>
