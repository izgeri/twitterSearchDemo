# twitterSearchDemo
Brief, PHP- and JQuery-based Twitter API search demo

## About this app

This is a TWITTER SEARCH UTILITY. It allows you to input a Twitter handle, # tweets, search string, and/or a location, and it returns a list of tweets that match the search.

Future work that would make this page more interesting / improve this project:
* Update the app to query the Google Maps Geocoding API on the server side, to prevent the API key being available on the client side
* Consider getting tweets from current location using the HTML5 geolocation API
* Add pagination, to improve handling / UX of requests for large numbers of tweets
* Add ability to log in and post a tweet
  * The TwitterOAuth library will make this pretty easy
* Add ability to save tweets


## Using this app

### Prerequisites

* [Git](https://git-scm.com/downloads) to download the repo and manage the source code
* [Docker](https://docs.docker.com/engine/installation) to manage dependencies
* [Summon](https://cyberark.github.io/summon/), with any vault that has a Summon provider available, to manage secrets. I used the [OSX-keyring provider](https://github.com/conjurinc/summon-keyring) when running this code.

### API keys required

* Sign up for an application account for the [Twitter API](https://apps.twitter.com/) by logging in with your Twitter account credentials and creating a new application. This will get you your Consumer Key and your Consumer Secret, which you will need to send requests to the Twitter API via TwitterOAuth. You can add these values to your OSX keyring by running:
```
security add-generic-password -s "summon" -a "twitter/api_key" -w "[Twitter API Key]"
security add-generic-password -s "summon" -a "twitter/api_secret" -w "[Twitter API Consumer Secret]"
```
* Get an API key for the [Google Maps API](https://developers.google.com/maps/documentation/geocoding/get-api-key.). To add
this key to your OSX keyring you can run:
```
security add-generic-password -s "summon" -a "google-maps-geocoding/api_key" -w "[Google API Key]"
```
You will also need to enable the Google Maps Javascript API in the [API console](https://console.developers.google.com/).

### Other creds required

The application also uses a PostgreSQL database to optionally store saved searches. If you are using the database, you will need to add the following entries to your vault:
```
twitter-search/pg_user
twitter-search/pg_password
twitter-search/pg_db
```
To add them to the OSX keyring, run `security add-generic-password` commands that follow the syntax above.

### Instructions

This repository contains two scripts to make running the app locally more
convenient. The `build-image.sh` runs a `docker build` command to build a Docker
image using the Dockerfile, and `run.sh` issues a `docker run` command to spin
up a container from the image created in the previous step.

If you have more than one summon provider installed, you will need to modify `run.sh` to reference the specific provider that should be used. See the [Summon documentation](https://github.com/cyberark/summon#flags) for more info.

The Dockerfile starts from the `php:7.2-apache` Docker image and uses [composer](https://getcomposer.org) to install the project dependencies.

To run this app locally in a Docker container, run the following commands:
```
git clone git@github.com:izgerij/twitterSearchDemo
cd twitterSearchDemo/
./build-image.sh
./run.sh
```
After running these commands, you should be able to view the app in your browser
by visiting `localhost:4000` in a browser window.

##### NOTE:
The Google API key is used client-side and will be visible to end users of this interface. To prevent unauthorized use and quota theft, you can use the Google API manager to restrict the key to certain HTTP referers or IP addresses. If you intend to make this application publicly accessible, doing so is strongly recommended.

### Running the unit tests
To run the unit tests, run
```
./build-image.sh
./test.sh
```
from within the base directory of the repository on your local machine. The `test.sh` script will run the test suite in a Docker container.

### Deploying to Minikube
If you want to install [Minikube](https://kubernetes.io/docs/tasks/tools/install-minikube/)
and [kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl/), you can
experiment with deploying (the database-less version of) the application to a local
Kubernetes cluster.

Instructions are provided in the [minikube-instructions.sh](minikube-instructions.sh) script.

## Included libraries:

* [FontAwesome](http://fontawesome.io/) by Dave Gandy, used for the loading spinner
* [TwitterOAuth](https://github.com/abraham/twitteroauth) used for querying the Twitter REST API
* [JQuery](jquery.org) used for async requests
* [Google Maps JavaScript API](https://developers.google.com/maps/documentation/javascript/) used for geocode generation from location string
* [PHPUnit](https://phpunit.de) used for unit tests
