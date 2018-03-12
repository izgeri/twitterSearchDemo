# twitterSearchDemo
Brief, PHP- and JQuery-based Twitter API search demo

## About this app

This is a TWITTER SEARCH UTILITY. It allows you to input a Twitter handle, # tweets, search string, and/or a location, and it returns a list of tweets that match the search.

Future work that would make this page more interesting:
* get tweets from current location using the HTML5 geolocation API
* pagination, to improve handling / UX of requests for large numbers of tweets
* add ability to log in and post a tweet
  * The TwitterOAuth library will make this pretty easy
* add ability to save tweets


## Using this app



* Download repository from github
* Install TwitterOAuth using Composer - the composer.json file is already included in the repository, so once you've downloaded the repos you can just run "php composer.phar install" in your working directory. If you don't already have Composer installed, you can find instructions for installing it here: https://getcomposer.org.
* Sign up for an application account for the Twitter API. Navigate to https://apps.twitter.com/ to log into your Twitter account and create a new application. This will get you your Consumer Key and your Consumer Secret, which you will need to send requests to the Twitter API via TwitterOAuth.
* Sign up for Google Maps API. To get an API key, go here: https://developers.google.com/maps/documentation/geocoding/get-api-key. Then update js/twitter.js to include your apiKey. You will also need to enable the Google Maps Javascript API in the API console. // TODO remove API key from JS
* Create a file keys.php with the following variables:
        $twitterConsumerKey, $twitterConsumerSecret, $googleApiKey
  to store the keys you created in the tasks above. Update index.php and findTweets.php to correctly set the path for this file; in general, it's best practice NOT to include this file in a publicly accessible directory (but elsewhere on the server or in a database). If you do include it in your publicly accessible directory, you can direct Apache to ignore the directory using a command like:
`<directorymatch “^/keys/“>
  Order deny,allow
  Deny from all
</directorymatch>`
added to your httpd.conf file. This will prevent end users from browsing and accessing your API keys.

##### NOTE:
The Google API key is used client-side and will be visible to end users of this interface. To prevent unauthorized use and quota theft, you can use the Google API manager to restrict the key to certain HTTP referers or IP addresses. If you intend to make this application publicly accessible, doing so is strongly recommended.

* To run the unit tests, run "./vendor/bin/phpunit tests/twitterSearchTest.php" from within the base directory of the repository.

## REQUIRED OS / LIBRARIES:

* A LAMP stack is required, and which one you use will depend on your OS. There's MAMP for Mac, WAMP for Windows, and LAMP for Linux.

## INCLUDED LIBRARIES:

* FontAwesome by Dave Gandy (used for the loading spinner) version 3.0, license information here: http://fontawesome.io/license/
* TwitterOAuth (used for querying the Twitter REST API) version 0.7.4, licensed under MIT License (https://github.com/abraham/twitteroauth/blob/master/LICENSE.md)
* JQuery (used for async requests) version 3.1.0, license information here: jquery.org/license
* Google Maps JavaScript API (used for geocode generation from location string) version 3.28, license information here: https://developers.google.com/maps/terms- PHPUnit (used for unit tests) version 5.7, license information here: https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.copyright
