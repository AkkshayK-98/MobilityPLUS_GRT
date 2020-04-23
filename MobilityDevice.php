<?php

//check that user is loged in
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
	$user = $_SESSION["id"];
	

	require_once('include/config.php');
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	$err = mysqli_connect_error();
	if ($err != null) {
		$errmsg = "<p>Cannot connect to database<p>" . $err;
		exit($errmsg);
	}

	$sql = "SELECT * FROM users WHERE user_id = $user";
	$result = mysqli_query($connection, $sql);

	if ($row = mysqli_fetch_assoc($result)) {
		$response = $row["default_mobility_device"];
	}
	echo $response;
}else{
	echo "Error";
}
?>