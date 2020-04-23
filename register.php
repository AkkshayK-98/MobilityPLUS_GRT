<?php
    session_start();
    include_once 'include/config.php';

    if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true){
        header("location: dashboard.php");
        exit;
    }

    function filter_name($field){
        //Sanitize name
        $field = filter_var(trim($field), FILTER_SANITIZE_STRING);

        //Validate name
        if(filter_var($field, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
            return $field;
        }else{
            return FALSE;
        }
    }

    function filter_address($field){
        //Validate address
        if(filter_var($field, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\d+\s[A-z]+\s[A-z]+/")))){
            return $field;
        }else{
            return FALSE;
        }
    }

    function filter_postal_code($field){
        //Validate postal code
        if(filter_var($field, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/")))){
            return $field;
        }else{
            return FALSE;
        }
    }

    function filter_email($field){
        //Sanitize email
        $field = filter_var(trim($field),FILTER_SANITIZE_EMAIL);

        //Validate email
        if(filter_var($field, FILTER_VALIDATE_EMAIL)){
            return $field;
        }else{
            return FALSE;
        }
    }
    
    function filter_uid($field){
        //Sanitize email
        $field - filter_var(trim($field),FILTER_SANITIZE_NUMBER_INT);

        //Validate email
        if(filter_var($field, FILTER_VALIDATE_INT)){
            return $field;
        }else{
            return FALSE;
        }
    }

    function filter_pwd($field){
        $uppercase = preg_match('@[A-Z]@', $field);
        $lowercase = preg_match('@[a-z]@', $field);
        $number = preg_match('@[0-9]@', $field);
        $special_char = preg_match('@[^\w]@', $field);

        if(!$uppercase || !$lowercase || !$number || !$special_char || strlen($field)<8){
            $password_err = "Password should be atleast 8 chars in length and contain atleast one uppercase letter, one lowercase letter, one number and one special character";
            return FALSE;
        }else{
            return $field;
        }

    }

    $email = $password = $r_password = $id = $fname = $lname = $h_a = $h_p = $h_c = $d_m_d = "";
    $email_err = $password_err = $r_password_err = $id_err = $fname_err = $lname_err = $h_a_err = $h_p_err = $h_c_err = $d_m_d_err ="";

    #Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
        // Error checking
        if (!$db) {
            print "<p>Error - Could not connect to MySQL</p>";
            exit;
        }
        $error = mysqli_connect_error();

        if ($error != null) {
            $output = "<p>Unable to connet to database</p>" . $error;
            exit($output);
        }


        // Validate first name
        if(empty($_POST["fname"])){
            $fname_err = "Please enter your first name.";
        } else{
            $fname = filter_name($_POST["fname"]);
            if($fname == FALSE){
                $fname_err = "Please enter a valid first name.";
            }
        }

        // Validate last name
        if(empty($_POST["lname"])){
            $lname_err = "Please enter your last name.";
        } else{
            $lname = filter_name($_POST["lname"]);
            if($lname == FALSE){
                $lname_err = "Please enter a valid last name.";
            }
        }

        // Validate home address
        if(empty($_POST["h_a"])){
            $h_a_err = "Please enter your home address";
        } else{
            $h_a = filter_address($_POST["h_a"]);
            if($h_a == FALSE){
                $h_a_err = "Please enter a valid home address.";
            }
        }

        // Validate postal code
        if(empty($_POST["h_p"])){
            $h_p_err = "Please enter your postal code.";
        } else{
            $h_p = filter_postal_code($_POST["h_p"]);
            if($h_p == FALSE){
                $h_p_err = "Please enter a valid home postal code.";
            }
        }

        // Validate email
        if(empty($_POST["email"])){
            $email_err = "Please enter your email.";
        } else{
            $email = filter_email($_POST["email"]);
            if($email != FALSE){
                $query = "SELECT email FROM users WHERE email = ?";
                if($stmt = mysqli_prepare($db, $query)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    
                    // Set parameters
                    //$param_username = trim($_POST["username"]);

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        /* store result */
                        mysqli_stmt_store_result($stmt);
                        
                        if(mysqli_stmt_num_rows($stmt) == 1){
                            $email_err = "This username is already taken.";
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
            }
            else{
                $email_err = "Please enter a valid email.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }

        // Validate id
        if(empty($_POST["id"])){
            $id_err = "Please enter your MobilityPLUS ID.";
        } else{
            $id = filter_uid($_POST["id"]);
            if($id == FALSE){
                $id_err = "Please enter a valid MobilityPLUS ID.";
            }
        }

        // Validate password
        if(empty($_POST["pwd"])){
            $password_err = "Please enter your password.";
        } else{
            $password = filter_pwd($_POST["pwd"]);
            if($password == FALSE){
                if($password_err == ""){
                    $password_err = "Please enter a valid password.";
                }
            }
        }

        // Validate repeat password
        if(empty($_POST["r_pwd"])){
            $r_password_err = "Please enter your password.";
        } else{
            $r_password = filter_pwd($_POST["r_pwd"]);
            if($r_password == FALSE){
                $r_password_err = "Please enter a valid password.";
            }else{
                if($r_password != $password){
                    $r_password_err = "Passwords do not match";
                    $r_password = FALSE;
                }
            }
        }
        $d_m_d=$_POST["d_m_d"];
        $h_c=$_POST["h_c"];
        if(empty($password_err) && empty($fname_err) && empty($lname_err) && empty($email_err) && empty($id_err) && empty($r_password_err) && empty($h_a_err) && empty($h_p_err)){
            $hash_pwd = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT Into users (user_id, first_name, last_name, email, password, home_address, home_city, home_postal, default_mobility_device) VALUES(?,?,?,?,?,?,?,?,?)";

            if ($statement = mysqli_prepare($db, $query)) {

                // bind parameters s - string,
                $result = mysqli_stmt_bind_param($statement, 'sssssssss', $id, $fname, $lname, $email, $hash_pwd, $h_a, $h_c, $h_p, $d_m_d);
                if (!$result) {
                    print "<p>bounding error</p>";
                }
                // execute query
                $result = mysqli_stmt_execute($statement);

                if ($result) {
                    session_start();
                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $email;
                    header("location: dashboard.html");
                } else {
                    print "Mysql insert Error" . mysqli_stmt_error($statement);
                }
            } else {
                print "<p>Error on prepare</p>";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>

    <link href="reset.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
    <link href="css/styles.css" rel="stylesheet">

    <style type="text/css">
        body {
            color: #999;
            background: #f3f3f3;
            font-family: 'Roboto', sans-serif;
        }
        .form-control {
            border-color: #eee;
            min-height: 41px;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #1C80EB;
        }
        .form-control, .btn {        
            border-radius: 3px;
        }
        .signup-form {
            width: 800px;
            margin: 0 auto;
            padding: 30px 0;
        }
        .signup-form h2 {
            color: #333;
            margin: 0 0 30px 0;
            display: inline-block;
            padding: 0 30px 10px 0;
            border-bottom: 3px solid #1C80EB;
        }
        .signup-form form {
            color: #999;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .signup-form .form-group {
            margin-bottom: 20px;
        }
        .signup-form label {
            font-weight: normal;
            font-size: 13px;
        }
        .signup-form input[type="checkbox"] {
            margin-top: 2px;
        }
        .signup-form .btn {        
            font-size: 16px;
            font-weight: bold;
            background: #1C80EB;
            border: none;
            margin-top: 20px;
            min-width: 140px;
        }
        .signup-form .btn:hover, .signup-form .btn:focus {
            background: lightblue;
            outline: none !important;
        }
        .signup-form a {
            color: #1C80EB;
            text-decoration: underline;
        }
        .signup-form a:hover {
            color: lightblue;
            text-decoration: none;
        }
        .signup-form form a {
            color: #1C80EB;
            text-decoration: none;
        }	
        .signup-form form a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <img src="images/grt-logo.PNG">
        <h1 class="title">MobilityPLUS Online Booking</h1>
    </header>

    <main class="signup-form">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="form-horizontal" method="post">
            <div class="col-xs-8 col-xs-offset-4">
                <h2>Sign Up</h2>
            </div>	
            <div class="form-group">
                <label class="control-label col-xs-4">First Name</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="fname" required="required">
                    <span><?php echo $fname_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Last Name</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="lname" required="required">
                    <span><?php echo $lname_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">MobolityPLUS ID</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="id" required="required">
                    <span><?php echo $id_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Email</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="email" required="required">
                    <span><?php echo $email_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Home Address</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="h_a" required="required">
                    <span><?php echo $h_a_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Home Postal Code</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="h_p" required="required">
                    <span><?php echo $h_p_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Home City</label>
                <div class="col-xs-8">
                    <select name="h_c">
                        <option value="Kitchener">Kitchener</option>
                        <option value="Waterloo">Waterloo</option>
                        <option value="Cambridge">Cambridge</option>
                    </select>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Default Mobility Device</label>
                <div class="col-xs-8">
                    <select name="d_m_d">
                        <option value="none" selected>None</option>
                        <option value="wheelchair">Wheelchair (manual)</option>
                        <option value="wheelchairElectric">Wheelchair (electric)</option>
                        <option value="guidedog">Guide dog</option>
                        <option value="walker">Walker</option>
                        <option value="walkingCane">Walking cane</option>
                        <option value="whiteCane">White cane (for blind)</option>
                        <option value="walkingCane">Walking cane</option>
                    </select>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Password</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="pwd" required="required">
                    <span><?php echo $password_err;?></span><br>
                </div>        	
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4">Repeat Password</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="r_pwd" required="required">
                    <span><?php echo $r_password_err;?></span><br>
                </div>        	
            </div>
            
            <div class="form-group">
                <div class="col-xs-8 col-xs-offset-4">
                    <p><label class="checkbox-inline"><input type="checkbox" required="required"> I accept the <a href="#">Terms of Use</a> &amp; <a href="#">Privacy Policy</a>.</label></p>
                    <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
                </div>  
            </div>	
        </form>
        <div class="text-center">Already have an account? <a href="index.html">Login here</a></div>
    </main>
</body>
</html>
