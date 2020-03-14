<?php
include_once 'include/config.php';


//get values from post
$userId = 111; //temporary
$pickUpDate = $_POST["pickupDate"];
$pickUpTime = $_POST["pickupTime"];
$pickUpAddress = $_POST["pickupaddress"];
$pickupCity = $_POST["pickupCity"];
$pickupPostalcode = $_POST["pickupPostalcode"];
$pickUpNotes = $_POST["pickupNotes"];
$returnTripDate = $_POST["returnTripDate"];
$returnTripTime = $_POST["returnTripTime"];
$returnTripAddress = $_POST["returnTripaddress"];
$returnTripCity = $_POST["returnTripCity"];
$returnTripPostalcode = $_POST["returnTripPostalcode"];
$returnTripNotes = $_POST["returnTripNotes"];
$device = $_POST["device"];
$guest = $_POST["guest"];


//notes can be empty
if (empty($pickUpNotes)) {
    $pickUpNotes = "None";
}
if (empty($returnTripNotes)) {
    $returnTripNotes = "None";
}

//check for empty values
if (
    !empty($pickUpDate) && !empty($pickUpTime) && !empty($pickUpAddress) && !empty($pickupCity) && !empty($pickupPostalcode) 
    && !empty($returnTripDate) && !empty($returnTripTime) && !empty($returnTripAddress) && !empty($returnTripCity) && !empty($returnTripPostalcode) && !empty($device) 
) {
    
    // Connect to MySQL
    $db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    
    // Error checking
    if (!$db) {
        print "Error - Could not connect to MySQL";
        exit;
    }
    $error = mysqli_connect_error();
    
    if ($error != null) {
        $output = "<p>Unable to connet to database</p>" . $error;
        exit($output);
    }


    $query = "INSERT Into bookings (user_id, pick_up_date, pick_up_time, pick_up_address, pick_up_city, pick_up_postal, pick_up_notes, return_date, return_time, return_pick_up_address, return_pick_up_city, return_pick_up_postal, return_pick_up_notes, mobility_device, guests) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($statement = mysqli_prepare($db, $query)) {
        
        // bind parameters s - string,
        $result=mysqli_stmt_bind_param($statement, 'sssssssssssssss', $userId, $pickUpDate, $pickUpTime, $pickUpAddress, $pickupCity, $pickupPostalcode, $pickUpNotes, $returnTripDate, $returnTripTime, $returnTripAddress, $returnTripCity, $returnTripPostalcode, $returnTripNotes, $device, $guest);
        if (!$result) {
            print "bounding error";
        }
        // execute query
        $result = mysqli_stmt_execute($statement);
        
        if ($result) {
            print "Success";
        } else {
            print "Mysql insert Error".mysqli_stmt_error($statement);
        }
    }else{
        print "error on prepare";
    }
} else {
    print "All fields, other then notes, are required";
    die();
}
