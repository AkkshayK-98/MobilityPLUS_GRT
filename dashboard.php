<?php
session_start();

require_once('include/config.php');
$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
$err = mysqli_connect_error();
if ($err != null) {
	$errmsg = "<p>Cannot connect to database<p>" . $err;
	exit($errmsg);
}

$local_id = $_SESSION["id"];

$sql = "SELECT * FROM bookings WHERE bookings.user_id = $local_id ORDER BY bookings.pick_up_date, bookings.pick_up_time";
$result = mysqli_query($connection, $sql);
$row = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard</title>
	<link href="reset.css" rel="stylesheet">
	<link href="css/stylesdash.css" type="text/css" rel="stylesheet">
</head>

<body>
	<div class="content_main_container">

		<div class="content_sub_container">

			<div class="banner_container">
				<p class="grt_logo_container"><img class="grt_logo_image" src="images/grt-logo.png"></p>
				<p class="banner_title"><b>Welcome To The Dashboard</b></p>
			</div>

			<nav class="navbar">
				<button class="navbar_button" onclick="window.location.href='booking.html'">Book A Trip</button>
				<button class="navbar_button" onclick="window.location.href='contact_userview.html'">Contact Us</button>
				<button class="navbar_button" onclick="window.location.href='signout.html'">Sign Out</button>
			</nav>

			<div class="content">

				<div class="grt_twitter_feed">
					<a class="twitter-timeline" data-width="220" data-height="460" href="https://twitter.com/GRT_ROW?ref_src=twsrc%5Etfw">Tweets by GRT_ROW</a>
					<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
				</div>

				<div class="trip_info">
					<?php


					/*
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
					$mobilitydevice = $row['mobility_device'];
					$guests = $row['guests'];
					*/
					//print_r($row);
					//echo "$row[$v]['trip_id']";
					?>
					<table class="info_display_table">
						<tr>
							<td><b><u>YOUR TRIPS</u></b></td>
						</tr>
						<?php
						if (!$result) {
							echo "<tr><td><b>No trips booked at this time.</b></td></tr>";
						}
						if ($result->num_rows == 0) {
							echo "<tr><td>No trips booked at this time...</td></tr>";
						} else {
							for ($i = 0; $i < $result->num_rows; $i++) {
								$moment = $row[$i]['pick_up_time'];
								$moment2 = $row[$i]['return_time'];
								echo '<tr class="info_display_table_row">';
								echo '<td class="info_display_table_row_content_1">Trip ID: ' . $row[$i]['trip_id'] . '<br><b>On:</b> ' . $row[$i]['pick_up_date'] . ', ' . date("H:i A", strtotime($moment)) . '
<b>Pick-up:</b> ' . $row[$i]['pick_up_address'] . ', ' . $row[$i]['pick_up_city'] . ', ' . $row[$i]['pick_up_postal'] . '.
<b>Destination:</b> ' . $row[$i]['return_pick_up_address'] . ', ' . $row[$i]['return_pick_up_city'] . ', ' . $row[$i]['return_pick_up_postal'] . '.
<b>Return on:</b>  ' . $row[$i]['return_date'] . ', ' . date("H:i A", strtotime($moment2)) . '';
								echo '<br><b>Moblity device:</b> '.$row[$i]['mobility_device'].'     <b>Guests:</b> '.$row[$i]['guests'];
								echo '<br><b>Pick up notes: </b>'.$row[$i]['pick_up_notes'];
								echo '<br><b>Return notes: </b>'.$row[$i]['return_pick_up_notes'];
								echo "</td>";
								echo '<td class="info_display_table_row_content_2">';
								echo '<center><form method="post"><input type="hidden" class="cancel_button" name=deltrip value=' . $row[$i]["trip_id"] . '><input type="submit" class="cancel_button" name=deltrips value="Cancel"></form></center>';
								echo "</td>";
								echo "</tr>";
								if ($i > 4) {
									break;
								}
							}
							if (array_key_exists('deltrips', $_POST)) {
								//echo $_POST['deltrip'];
								$sql2 = "DELETE FROM bookings WHERE bookings.trip_id = " . htmlspecialchars($_POST['deltrip']);
								$result = mysqli_query($connection, $sql2);
								header("Refresh:0");
							}
						}
						?>
					</table>

				</div>

			</div>

			<div class="footer">
				<p class="footer_text">All information used herein is property of Grand River Transit, Waterloo ON.</p>
			</div>

		</div>

	</div>
</body>

</html>