<?php

// include the keys file
include "keys/keys.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Twitter Search Utility</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src='lib/jquery.min.js'></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.28&key=<?php echo $googleApiKey; ?>"></script>
	<script src="https://use.fontawesome.com/14beef1954.js"></script>
	<script type="text/javascript" src="js/twitter.js"></script>
</head>

<body>
	<div id="searchBox">

		<table width="100%">
		<tr><td align="right">twitter username(s):</td>
		<td align="left"><input type="text" id="searchUsername" /></td></tr>

		<tr><td align="right"># tweets to show:</td>
		<td align="left"><input type="text" id="searchNumTweets" /></td></tr>

		<tr><td align="right">search string:</td>
		<td align="left"><input type="text" id="searchTerm" /></td></tr>

		<tr><td align="right">city, state or zip (opt.):</td>
		<td align="left"><input type="text" id="searchLocation" /></td></tr>
		<tr><td align="center" colspan="2"><input type="checkbox" id="searchIgnoreRetweets" /> ignore retweets
		<!-- or <input type="checkbox" id="searchUseCurrentLocation" /> get tweets from my current location<br /><br />--><br />
		<tr><td colspan="2" align="center"><input type="button" id="searchButton" value="find tweets" onclick="findTweets();" /></td></tr>
		<tr><td colspan="2" align="center"><span id="searchError" class="warningText"></span></td></tr></table>
		<input type="hidden" id="googleApiKey" value="<?php echo $googleApiKey; ?>" />
	</div>

	<div id="searchResults">
	</div>

</body>

</html>
