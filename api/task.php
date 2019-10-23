<?php
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    // Declare the credentials to the database
$dbconnecterror = false;
$dbh = NULL;
    
require_once 'credentials.php';
    
try{
    $conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
	//Database issues were encountered.
    //$dbconnecterror = TRUE;
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
        exit();
	}
    //Decoding the json body from the request.
    $task = json_decode(file_get_contents('php://input'), true);
    
    //IF the task == null, then exit, because there is nothing to POST
    if ($task === null) {
        http_response_code(400);
        exit(); //no data in body.
    }
    
    if (array_key_exists('completed', $task)) {
        $complete = $task["completed"];
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
    

	if (!$dbconnecterror) {
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
	} else {
        http_response_code(504); //Gateway timeout
        echo "database error";
        exit();
}
}else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    //add new code here for adding a task
    //don't need an id here because it hasn't been created yet.
    
    if (array_key_exists('fin', $_POST)) {
        $complete = 1;
    } else {
        $complete = 0;
    }
    
    if (empty($_POST['finBy'])) {
        $finBy = null;
    } else {
        $finBy = $_POST['finBy'];
    }
    
    if (!$dbconnecterror) {
        try {
            $sql = "INSERT INTO doList (complete, listItem, finishDate) VALUES (:complete, :listItem, :finishDate)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(":complete", $complete);
            $stmt->bindParam(":listItem", $_POST['listItem']);
            $stmt->bindParam(":finishDate", $taskDate);
            $response = $stmt->execute();
            
            header("Location: index.php");
            
        } catch (PDOException $e) {
            header("Location: index.php?error=add");
        }
    } else {
        header("Location: index.php?error=add");
    }
    
}else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    //add new code here for deleting a task
    //Need ID to delete a specific task.
    if (!$dbconnecterror) {
        try {
            $sql = "DELETE FROM doList where listID = :listID";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(":listID", $_POST['listID']);
            
            $response = $stmt->execute();
            
            header("Location: index.php");
            
        } catch (PDOException $e) {
            header("Location: index.php?error=delete");
        }
    } else {
        header("Location: index.php?error=delete");
    }
    
} else {
    http_response_code(405);//method not allowed
    echo "expected PUT, POST, or DELETE";
    exit();
} //PUT
?>
