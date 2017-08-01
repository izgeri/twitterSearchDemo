<?php

class twitterSearch {

	// list of search-related member variables
	private $usernameArray; // array of usernames
	private $usernames; // comma-separated list of usernames
	private $usernameQuery; // query-formatted list of usernames
	private $latLong; // string with lat / long separated by a comma
	private $searchTerm; // search string
	private $numTweets; // numeric value with number of tweets to search

	// list of twitter / oauth related member variables
	private $twitter; // TwitterOAuth object
	private $consumerKey; // Twitter Consumer Key
	private $consumerSecret; // Twitter Consumer Secret
	private $oauthToken; // Twitter Token (not used)
	private $oauthTokenSecret; // Twitter Token Secret

	// list of API call related member variables
	private $searchArray; // array of Twitter search terms
	private $response; // response from last request
	private $tweetArray; // array of tweets
	private $oembedUrl; // URL to send request for oembed-formatted tweet
	private $curl;

	/*
	 * var consumerKey - application consumer key
	 * var consumerSecret - application consumer secret
	 * var oauthToken - client auth token (not yet used) - opt.
	 * var oauthTokenSecret - client auth secret (used as bearer if
	 *     oauthToken is blank) - opt.
	 */
	function __construct($consumerKey, $consumerSecret, $oauthToken = NULL,
		$oauthTokenSecret = NULL) {

		// set the twitter-specific API info
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->oauthToken = $oauthToken;
		$this->oauthTokenSecret = $oauthTokenSecret;

		$this->oembedUrl = 'https://publish.twitter.com/oembed';
	}

	/*
	 * function to get member variable consumer key
	 */
	public function getConsumerKey() {

		return $this->consumerKey;
	}

	/*
	 * function to get member variable consumer secret
	 */
	public function getConsumerSecret() {

		return $this->consumerSecret;
	}

	/*
	 * function to set the curl member variable
	 */
	public function setCurl(curl $ch) {

		$this->curl = $ch;
	}

	/*
	 * function to set member variable twitter (TwitterOAuth object)
	 */
	public function setTwitter(Abraham\TwitterOAuth\TwitterOAuth $twitter) {

		$this->twitter = $twitter;
	}
	
	/*
	 * function to set member variable username 
	 * var $usernames - space-separated string of usernames
	 */
	public function setUsernames($usernames) {

		$this->usernames = trim($usernames);

		$this->loadUsernameVariables();

		$this->lookupUsers();
	}

	/*
	 * function to return member variable usernames
	 * string of space separated usernames
	 */
	public function getUsernames() {

		return $this->usernames;
	}

	/*
	 * function to return member variable usernameArray
	 */
	public function getUsernameArray() {

		return $this->usernameArray;
	}

	/*
	 * function to return member variable usernameQuery
	 * twitter-formatted query for set of usernames for search
	 */
	public function getUsernameQuery() {

		return $this->usernameQuery;
	}

	/*
	 * function to set member variable latLong
	 * var $latLong - comma-separated lat/long pair
	 */
	public function setLatLong($latLong) {

		$this->latLong = $latLong;
	}

	/*
	 * function to return member variable with comma-separated lat/long pair
	 */
	public function getLatLong() {

		return $this->latLong;
	}

	/*
	 * function to set member variable searchTerm
	 * var $searchTerm - string search term
	 */
	public function setSearchTerm($searchTerm) {

		$this->searchTerm = $searchTerm;
	}

	/*
	 * function to get string member variable searchTerm
	 */
	public function getSearchTerm() {

		return $this->searchTerm;
	}

	/*
	 * function to set member variable numTweets
	 * var $numTweets - numeric value for # tweets
	 */
	public function setNumTweets($numTweets) {

		$this->numTweets = $numTweets;
	}

	/*
	 * function to get numeric member variable numTweets
	 */
	public function getNumTweets() {

		return $this->numTweets;
	}

	/*
	 * function to get search array passed to twitter search
	 */
	public function getSearchArray() {

		return $this->searchArray;
	}

	/*
	 * function to instantiate TwitterOAuth object with the correct
	 * credentials, either application-only auth or
	 * authenticating for a specific user (TODO)
	 */
	public function authenticateApp(Abraham\TwitterOAuth\TwitterOAuth $twitter) {

		$this->setTwitter($twitter);

		if (is_null($this->oauthToken) && is_null($this->oauthTokenSecret)) {

			// use application-only auth

			// get auth token
			$creds = $this->twitter->oauth2('oauth2/token',
				['grant_type' => 'client_credentials']);

			if ($this->twitter->getLastHttpCode() != '200') {

				// authentication failed
				throw new Exception("Error authenticating app.");
			}

			$this->oauthTokenSecret = $creds->access_token;
		}

		// TODO handle Twitter user-specific auth

		$authArray = array('consumerKey' => $this->consumerKey,
			'consumerSecret' => $this->consumerSecret,
			'oauthToken' => $this->oauthToken,
			'oauthTokenSecret' => $this->oauthTokenSecret);

		return $authArray;
	}

	/*
	 * function to search Twitter
	 * var username - space-separated list of Twitter usernames (strings) - opt.
	 * var numTweets - numeric # tweets to return
	 * var searchTerm - string search term (opt.)
	 * var latLong - comma-separated latitude / longitude pair (opt.)
	 * var ignoreRetweets - boolean of whether to remove retweets from the
	 *     search results
	 */
	public function search($usernames, $numTweets, $searchTerm, $latLong,
		$ignoreRetweets) {

		$this->setUsernames($usernames);
		$this->setNumTweets($numTweets);
		$this->setSearchTerm($searchTerm);
		$this->setLatLong($latLong);

		$query = $this->usernameQuery;
		$query .= $query ? " " . $this->searchTerm : $this->searchTerm;

		if ($ignoreRetweets) {

			$query .= " -filter:retweets";
		}

		$this->searchArray = array("q" => $query,
			"count" => $this->numTweets);

		if ($this->latLong) {

			$this->searchArray['geocode'] = $this->latLong . ",5mi";
		}

		$this->response = $this->twitter->get("search/tweets",
			$this->searchArray);

		$this->loadTweets();

		return $this->tweetArray;
	}

	/*
	 * function to parse the search response and populate the Tweet
	 * array with a set of block-quoted tweets
	 */
	private function loadTweets() {

		$this->tweetArray = array();

		if (sizeof($this->response->statuses) == 0) {

			throw new Exception("Sorry, your search returned no results.");
		}

		$urlArray = array();
		foreach ($this->response->statuses as $tweet) {

			$statusId = $tweet->id;
			$text = $tweet->text;
			$user = $tweet->user->name;
			$username = $tweet->user->screen_name;
			$userString = $user .  ' (@' . $username . ')';
			$creationTime = $tweet->created_at;
			$createDate = date('F d, Y', strtotime($creationTime));

			$url = 'https://twitter.com/' . $username . '/status/' . $statusId;

			$tweet = '<blockquote class="twitter-tweet" data-lang="en">';
			$tweet .= '<p lang="en" dir="ltr">' . $text . '</p>';
			$tweet .= ' &mdash; ' . $userString;
			$tweet .= ' <a href="' . $url . '">' . $createDate . '</a>';
			$tweet .= '</blockquote>';

			$urlArray[$url] = $tweet;
		}

		foreach ($urlArray as $url => $textTweet) {

			$tweet = $this->getOembedTweet($url);

			if (!$tweet) {

				$tweet = $textTweet;
			}

			array_push($this->tweetArray, $tweet);
		}
	}

	/*
	 * function to load member variables related to usernames, stored
	 * in different formats (array, twitter query string)
	 */
	private function loadUsernameVariables() {

		$this->usernameArray = $this->usernames ? explode(' ', $this->usernames) : array();

		$this->usernameQuery = '';
		foreach ($this->usernameArray as $username) {
			
			$this->usernameQuery .= "from:" . $username . " OR ";
		}

		$this->usernameQuery = rtrim($this->usernameQuery, " OR ");
	}

	/*
	 * function to run a users/lookup request to check for valid usernames
	 */
	private function lookupUsers() {

		$userErrors = '';
		foreach ($this->usernameArray as $username) {

			$this->response = $this->twitter->get("users/show",
				array("screen_name" => $username));

			if ($this->twitter->getLastHttpCode() == '404') {

				$userErrors .= $username . ", ";
			}
		}

		if ($userErrors) {

			$userErrors = rtrim($userErrors, ", ");
			$error = "The following username(s) are not valid: " .
				$userErrors . ".";

			throw new Exception($error);
		}
	}

	private function getOembedTweet($url) {

		$tweet = '';

		// oembed requests don't require oauth, so we can just send
		// a curl request directly
		$curlTimeout = 30;
		$requestUrl = $this->oembedUrl . "?url=" . urlencode($url);

		$this->curl->initiate();
		$this->curl->setOptArray(
			array(
                        CURLOPT_URL => $requestUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => $curlTimeout));
		$this->response = $this->curl->execute();

		if (!$this->curl->getErrorNumber() && $this->response !== FALSE) {

                        $this->response = json_decode($this->response);

                        // only get the oembed tweet if the query didn't fail
                        // if it failed, the text tweet compiled from the
                        // search data will be used as a fallback
                        $tweet = $this->response->html;
                }

		$this->curl->close();

		return $tweet;
	}
}

?>
