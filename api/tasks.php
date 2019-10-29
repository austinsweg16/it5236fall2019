<?php
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
		
		// listID  -> listID
		// listItem -> taskName
		// finishDate -> taskDate
		// complete -> completed
		
		$final = [];
		foreach($result as $task) {
			// Copy the task info into the new, proper keys
			$task['taskName'] = $task['listItem'];
			$task['taskDate'] = $task['finishDate'];
			$task['completed'] = $task['complete'];
			$task['completed'] = $task['completed'] == "1" ? true : false;

			// Delete the old keys
			unset($task['listItem'], $task['finishDate'], $task['complete']);
			
			$final[] = $task;
		}
		
		http_response_code(200);
		echo json_encode($final);
		exit();
	} catch (PDOException $e) {
		http_response_code(500);
		echo 'cannot select tasks';
		exit();
	}
}
