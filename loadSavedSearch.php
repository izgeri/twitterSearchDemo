<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function ($class) {
	if(file_exists("src/class.{$class}.php")){
		include_once("src/class.{$class}.php");
	}
});

include "database.php";

$searchId = $_POST['searchId'];

$error = '';
$response = array();

try {

	// add the current search
	$statement = $conn->prepare("
		SELECT * FROM searches
		WHERE id = :searchId");
	$statement->execute(array(
		"searchId" => $searchId));

	$response = $statement->fetch(PDO::FETCH_ASSOC);

} catch (Exception $exc) {

	$error = $exc->getMessage();
}

if ($error) {

	$response['error'] = $error;
}

echo json_encode($response);

?>
