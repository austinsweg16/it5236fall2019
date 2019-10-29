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

//Update a task
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	if(array_key_exists('listID', $_GET)){
		$listID = $_GET['listID'];
	}else {
        http_response_code(504);
		echo 'missing list id';
        exit();
	}
	
    //Decoding the json body from the request.
    $task = json_decode(file_get_contents('php://input'), true);
    
    //IF the task == null, then exit, because there is nothing to POST
    if ($task === null) {
        http_response_code(400);
		echo 'missing json body';
        exit(); //no data in body.
    }
    
    if (array_key_exists('completed', $task)) {
        $complete = $task['completed'] ? 1 : 0;
	} else {
        http_response_code(400);
        exit();
	}
    
    if (array_key_exists('taskName', $task)) {
        $taskName = $task["taskName"];
    } else {
        http_response_code(400);
        exit();
    }

    if (array_key_exists('taskDate', $task)) {
        $taskDate = $task["taskDate"];
    } else {
        http_response_code(400);
        exit();
    }
    
	try {
		$sql = "UPDATE doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate WHERE listID=:listID";
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(":complete", $complete);
		$stmt->bindParam(":listItem", $taskName);
		$stmt->bindParam(":finishDate", $taskDate);
		$stmt->bindParam(":listID", $listID);
		$response = $stmt->execute();
		http_response_code(204);
		exit();
		
	} catch (PDOException $e) {
		http_response_code(504); //Gateway timeout
		echo "database maybe exception fields";
		exit();
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        //don't need an id here because it hasn't been created yet.
		
	//Decoding the json body from the request.
    $task = json_decode(file_get_contents('php://input'), true);
    
    //IF the task == null, then exit, because there is nothing to POST
    if ($task === null) {
        http_response_code(400);
		echo 'missing json body';
        exit(); //no data in body.
    }
    
    if (array_key_exists('completed', $task)) {
		$complete = $task['completed'] ? 1 : 0;
	} else {
		http_response_code(400);
		echo 'missing completed key';
        exit();
	}
     
	if (array_key_exists('taskName', $task)) {
        $taskName = $task["taskName"];
    } else {
        http_response_code(400);
        exit();
    }

    if (array_key_exists('taskDate', $task)) {
        $taskDate = $task["taskDate"];
    } else {
        http_response_code(400);
        exit();
    }
    
	try {
		$sql = "INSERT INTO doList (complete, listItem, finishDate) VALUES (:complete, :listItem, :finishDate)";
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(":complete", $complete);
		$stmt->bindParam(":listItem", $taskName);
		$stmt->bindParam(":finishDate", $taskDate);
		$response = $stmt->execute();
		http_response_code(201);
		exit();
		
		
	} catch (PDOException $e) {
		http_response_code(500);
		exit();
	}
    
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    //add new code here for deleting a task
    //Need ID to delete a specific task.
	
	if(array_key_exists('listID', $_GET)){
		$listID = $_GET['listID'];
	}else {
        http_response_code(504);
		echo 'missing list id';
        exit();
	}
	try {
		$sql = "DELETE FROM doList where listID = :listID";
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(":listID", $listID);
		$stmt->execute();
	
		http_response_code(204);
		exit();
		
	} catch (PDOException $e) {
		http_response_code(500);
		exit();
	}
    
} else {
    http_response_code(405);//method not allowed
    echo "expected PUT, POST, or DELETE";
    exit();
} //PUT
