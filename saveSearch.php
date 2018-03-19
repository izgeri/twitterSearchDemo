<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function ($class) {
	if(file_exists("src/class.{$class}.php")){
		include_once("src/class.{$class}.php");
	}
});

include "database.php";

$username = $_POST['usernames'];
$numTweets = $_POST['numTweets'];
$term = $_POST['searchTerm'];
$location = $_POST['location'];
$ignoreRetweets = $_POST['ignoreRetweets'];

$error = '';
$optionArray = array();

$searchSummary = '';

if ($username) {
	$searchSummary .= $username;
}
if ($term) {
	if ($searchSummary) {
		$searchSummary .= '/' . $term;
	} else {
		$searchSummary .= $term;
	}
}
$searchSummary .= ' (' . $numTweets . ')';
if ($location) {
	$searchSummary .= ' - ' . $location;
}
if ($ignoreRetweets != 'false') {
	$searchSummary .= ', no rt';
}

try {

	// add the current search
	$statement = $conn->prepare("
		INSERT INTO searches
		(username, numTweets, term, location, ignoreRetweets)
		VALUES
		(:username, :numTweets, :term, :location, :ignoreRetweets)");
	$statement->execute(array(
		"username" => $username,
		"numTweets" => $numTweets,
		"term" => $term,
		"location" => $location,
		"ignoreRetweets" => $ignoreRetweets));

	$searchId = $conn->lastInsertId();

} catch (Exception $exc) {

	$error = $exc->getMessage();
}

$response = array('id' => $searchId, 'desc' => $searchSummary);

if ($error) {

	$response['error'] = $error;
}

echo json_encode($response);

?>
