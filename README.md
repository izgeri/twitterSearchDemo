# twitterSearchDemo
Brief, PHP- and JQuery-based Twitter API search demo

## ABOUT THIS APP:

This is a TWITTER SEARCH UTILITY. Given the short timeline and my other obligations, I wanted to use tools I was mostly already familiar with to get this product out of the door quickly. I am currently using mostly straight up JavaScript / JQuery and PHP in the app I have been working on for the past four plus years, so it made sense to find a way to implement this using the same languages. My second choice would have been the security protocols; the security for the app I'm currently working on was already in place when I started working there, but having done some work to maintain it I would be interested in experimenting with more modern and secure ways to authenticate end users.  The postcard creator seemed like it would be much better to make using more modern JS tools, which I'm not yet familiar enough with to implement quickly so that was less of a natural choice for me.
I've never interacted with the Twitter API or had to imbed tweets into a dynamic webpage before, so it was interesting to learn as I went in implementing this app. Given more time to work on it, I would implement the following (and in this order):
* get tweets from current location using the HTML5 geolocation API
* pagination, to improve handling / UX of requests for large numbers of tweets
* add ability to log in and post a tweet
  * The TwitterOAuth library will make this pretty easy
* ability to hide / show cards
  * I'm not sure why this is super useful, but it would be easy enough to implement by adding a link to the card that changes the object to display:none. It would require some reformatting of the way the cards are loaded onto the page at current.
* ability to drag / drop cards
  * Also not sure why this would be useful, but there are many JS libraries that allow for drag / drop (like JQuery draggable / droppable) that could be used.

If desired, I can take more time and do the items on this list. Please let me know if you'd be interested in seeing these updates.


## DIRECTIONS FOR USE:

* Download repository from github
* Install TwitterOAuth using Composer - the composer.json file is already included in the repository, so once you've downloaded the repos you can just run "php composer.phar install" in your working directory. If you don't already have Composer installed, you can find instructions for installing it here: https://getcomposer.org.
* Sign up for an application account for the Twitter API. Navigate to https://apps.twitter.com/ to log into your Twitter account and create a new application. This will get you your Consumer Key and your Consumer Secret, which you will need to send requests to the Twitter API via TwitterOAuth.
* Sign up for Google Maps API. To get an API key, go here: https://developers.google.com/maps/documentation/geocoding/get-api-key. Then update js/twitter.js to include your apiKey. You will also need to enable the Google Maps Javascript API in the API console. // TODO remove API key from JS
* Create a file keys.php with the following variables:
        $twitterConsumerKey, $twitterConsumerSecret, $googleApiKey
  to store the keys you created in the tasks above. Update index.php and findTweets.php to correctly set the path for this file; in general, it's best practice NOT to include this file in a publicly accessible directory (but elsewhere on the server or in a database). If you do include it in your publicly accessible directory, you can direct Apache to ignore the directory using a command like:
<directorymatch “^/keys/“>
  Order deny,allow
  Deny from all
</directorymatch>
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
