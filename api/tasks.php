<?php
ini_set('display_errors', 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Declare the credentials to the database
$dbh = NULL;
    
require_once 'credentials.php';
    
try{
    $conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
	//Database issues were encountered.
    http_response_code(504);
    echo "Database timeout";
    exit();
}

// Get all tasks
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	try {
		$sql = "SELECT * FROM doList";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
		echo json_encode($result);
		exit();
	} catch (PDOException $e) {
		http_response_code(500);
		echo 'cannot select tasks';
		exit();
	}
}
