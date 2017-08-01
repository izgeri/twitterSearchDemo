/*
 * function to take in the search parameters, validate them, send
 * a JQuery post request to run the search, and update the display with the
 * results
 */
function findTweets(latLong) {

	var error = '';

	// get input field values; trim excess whitespace
	var usernames = $('#searchUsername').val().trim();
	var numTweets = $('#searchNumTweets').val().trim();
	var searchTerm = $('#searchTerm').val().trim();
	var locationString = $('#searchLocation').val().trim();
	var ignoreRetweets = $('#searchIgnoreRetweets').prop("checked");

	// perform actions required on first function call
	if (latLong === undefined) {

		// disable button to prevent multiple clicks
		$('#searchButton').prop("disabled", true);

		// clear out the error field - will update as run new error checks
		// on current inputs
		$('#searchError').html('');

		// clear out the current search results
		$('#searchResults').html('');
	}

	// if not searching by location, latLong will be set to an empty string
	if (!locationString) {
		var latLong = '';
	}

	// check the input fields for any issues before searching tweets

	// usernames will be validated in the PHP file (to ensure alpha-numeric
	// and underscores only, valid according to the twitter API, etc)
	// standardize format to be space-separated instead of comma or
	// semicolon separated
	usernames = usernames.replace(/[,;]/g, ' ');
	usernames = usernames.replace(/@/g, '');
	usernames = usernames.replace('  ', ' ');

	// validate numTweets is numeric
	if (!$.isNumeric(numTweets)) {

		error = 'Please enter a numeric value for the # tweets. ';
		$('#searchError').html(error);
		$('#searchButton').prop("disabled", false);
	}

	if (!usernames && !searchTerm && $('#searchError').html() == '') {

		error = "Please enter at least one username or a search string.";
		$('#searchError').html(error);
		$('#searchButton').prop("disabled", false);
	}

	// validate location string
	if (locationString && $('#searchError').html() == '' && latLong === undefined) {

		lookupLatLong(locationString);
	}

	// if there are no errors and we are not waiting for a lat / long
	// for the input location string, we can run the search
	if ($('#searchError').html() == '' && (!locationString || latLong !== undefined)) {

		// there are no errors, so send the request
		// first, indicate that the request is processing
		$('#searchResults').html('<i class="fa fa-spinner"></i>');

		// then, send the request
		$.post("findTweets.php",
			{
				usernames: usernames,
				numTweets: numTweets,
				searchTerm: searchTerm,
				latLong: latLong,
				ignoreRetweets: ignoreRetweets
			},
			function (response, status) {

				var result = JSON.parse(response);

				if (result.error) {

					$('#searchError').html(result.error);
					$('#searchResults').html('');

				} else {

					var searchResults = '';
					$('#searchResults').html('');
					for (var i = 0; i < result.tweets.length; i++) {

						var tweet = result.tweets[i];
						searchResults += tweet;
						$('#searchResults').append(tweet);
					}
				}

				$('#searchButton').prop("disabled", false);
			}
		);
	}
}

function lookupLatLong(locationString) {

	// get API key for Google Maps API
	var googleApiKey = $('#googleApiKey').val();

	$.get("https://maps.googleapis.com/maps/api/geocode/json",
		{
			'address' : locationString,
			'key' : googleApiKey
		},
		function (response, status) {

			var error = '';
			var results = response['results'];
			var getStatus = response['status'];
			var numResults = results.length;

			if (getStatus == "OK" && numResults == 1) {

				var lat = results[0]['geometry']['location']['lat'];
				var long = results[0]['geometry']['location']['lng'];
				findTweets(lat + ',' + long);

			} else if (getStatus == "OK" && numResults > 1) {

				error = "Please enter a more specific location; there were too many matches.";

			} else {

				error = "Please enter a valid location string.";
			}

			if (error) {

				$('#searchError').html(error);
				$('#searchButton').prop("disabled", false);
			}
		}
	);
}
