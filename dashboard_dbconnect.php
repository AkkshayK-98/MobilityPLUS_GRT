<?php

	// STEP 1 - establish db connection

	require_once('include/config.php');
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	$err = mysqli_connect_error();
	if ($err != null)
	{
		$errmsg = "<p>Cannot connect to database<p>" . $err;
		exit($errmsg);
	}

	// STEP 2 - fetch user_id FROM db(mobility_plus) table(users)
	// to identify user instance
	// [???] SANITIZE data to protect against SQL injection

	// STEP 3 - fetch trip info FROM db(mobility_plus) table(bookings)
	// corresponding to user instance
	// [???] SANITIZE data to protect against SQL injection
	$sql = "SELECT * FROM bookings WHERE bookings.user_id = users.user_id ORDER BY bookings.pick_up_date, bookings.pick_up_time";
	$result = mysqli_query($connection, $sql);
	$row = $result->fetch_array(MYSQLI_ASSOC);

	$tripid = $row['trip_id'];
	$userid = $row['user_id'];
	$pickupdate = $row['pick_up_date'];
	$pickuptime = $row['pick_up_time'];
	$pickupaddress = $row['pick_up_address'];
	$pickupcity = $row['pick_up_city'];
	$pickuppostal = $row['pick_up_postal'];
	$pickupnotes = $row['pick_up_notes'];
	$returndate = $row['return_date'];
	$returntime = $row['return_time'];
	$returnpickupaddress = $row['return_pick_up_address'];
	$returnpickupcity = $row['return_pick_up_city'];
	$returnpickuppostal = $row['return_pick_up_postal'];
	$returnpickupnotes = $row['return_pick_up_notes'];
	$mobilitydevie = $row['mobility_device'];
	$guests = $row['guests'];

	// STEP 4 expressing results
	// assuming only 5 most recent upcoming trips are displayed
	for($i = 0; $i < 5; $i++)
	{
		$msg = "";
	}

	// STEP 5 - should connection be terminated?
	// this might thwart real time updating attempts, such
	// as cancelling a trip using the cancel button
	// [*] PERHAPS we might close everything once sign out button is clicked
	mysqli_free_result($result);
	mysqli_close($connection);

	// STEP 5.5 (ONLY IF STEP 4 NOT approved)
	// configuring cancel trip and updating db accordingly
	// only table(bookings) is affected



?>