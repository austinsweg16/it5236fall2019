<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$url = "http://3.19.209.253/api/task.php?listID={$_POST['listID']}";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json'
	]);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	
	if ($http_status_code == 204) {
		header("Location: index.php");
	} else {
		header("Location: index.php?error=delete");
	}
}
