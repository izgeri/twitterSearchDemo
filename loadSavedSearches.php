<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function ($class) {
	if(file_exists("src/class.{$class}.php")){
		include_once("src/class.{$class}.php");
	}
});

include "database.php";

$error = '';
$response = array();

try {

	// add the current search
	$statement = $conn->prepare("
		SELECT * FROM searches");
	$statement->execute();


	while($search = $statement->fetch(PDO::FETCH_ASSOC)) {
		$searchId = $search['id'];
		$searchSummary = '';

		if ($search['username']) {
			$searchSummary .= $search['username'];
		}
		if ($search['term']) {
			if ($searchSummary) {
				$searchSummary .= '/' . $search['term'];
			} else {
				$searchSummary .= $search['term'];
			}
		}
		$searchSummary .= ' (' . $search['numtweets'] . ')';
		if ($search['location']) {
			$searchSummary .= ' - ' . $search['location'];
		}
		if ($search['ignoreretweets'] != 'false') {
			$searchSummary .= ', no rt';
		}

		array_push($response, array('id' => $searchId,
			'desc' => $searchSummary));
	}

} catch (Exception $exc) {

	$error = $exc->getMessage();
}

if ($error) {

	$response['error'] = $error;
}

echo json_encode($response);

?>
