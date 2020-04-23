<?php
    session_start();
    include_once 'include/config.php';

    if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true){
        header("location: dashboard.php");
        exit;
    }

    $username = $password = "";
    $username_err = $password_err = "";

    #Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        #checking if username is empty
        if(empty(trim($_POST["uname"]))){
            $username_err = "Please enter username";
        }
        else{
            $username = trim($_POST["uname"]);
        }

        #checking if password is empty
        if(empty(trim($_POST["pwd"]))){
            $password_err = "Please enter password";
        }
        else{
            $password = trim($_POST["pwd"]);
        }


        #Validate credentials
        if(empty($password_err) && empty($username_err)){
            //prepare sql statement

            // Connect to MySQL
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

            //query statement
            $query = "SELECT user_id, email, password FROM users WHERE email = ?";
            if($stmt = mysqli_prepare($db, $query)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Set parameters
                $param_username = $username;

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);

                    // Check if username exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct, so start a new session
                                session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;

                                // Redirect user to welcome page
                                header("location: dashboard.html");
                            }
                            else{
                                //display error statement for invalid password
                                $password_err = "Password is NOT VALID";
                            }
                        }
                    }
                    else{
                        //display error stament for invalid username
                        $username_err = "Email is NOT VALID";
                    }
                }
                else{
                    echo "OOPS, Something went wrong. Please try again later!";
                }
            }
            mysqli_stmt_close($stmt);  
            mysqli_close($db); 
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

    <link href="reset.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 

    <link href="css/styles.css" rel="stylesheet">

    <style type="text/css">
        body {
            color: #999;
            background: #f3f3f3;
            font-family: 'Roboto', sans-serif;
            font-size: 20pt;
        }
        .form-control {
            border-color: #eee;
            min-height: 41px;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #5cd3b4;
        }
        .form-control, .btn {        
            border-radius: 3px;
        }
        .signin-form {
            width: 800px;
            margin: 100px auto;
            padding: 30px 0;
        }
        .signin-form h2 {
            color: #333;
            margin: 0 0 30px 0;
            display: inline-block;
            padding: 0 30px 10px 0;
            border-bottom: 3px solid #5cd3b4;
        }
        .signin-form form {
            color: #999;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .signin-form .form-group {
            margin-bottom: 20px;
        }
        .signin-form label {
            font-weight: normal;
            font-size: 20pt;
        }
        .signin-form input[type="checkbox"] {
            margin-top: 2px;
        }
        .signin-form .btn {        
            font-size: 16px;
            font-weight: bold;
            background: #5cd3b4;
            border: none;
            margin-top: 20px;
            min-width: 140px;
        }
        .signin-form .btn:hover, .signup-form .btn:focus {
            background: #41cba9;
            outline: none !important;
        }
        .signin-form a {
            color: #5cd3b4;
            text-decoration: underline;
        }
        .signin-form a:hover {
            text-decoration: none;
        }
        .signin-form form a {
            color: #5cd3b4;
            text-decoration: none;
        }	
        .signin-form form a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <header>
        <img src="images/grt-logo.PNG">
        <h1 class="title">MobilityPLUS Online Booking</h1>
    </header>

    <main class="signin-form">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="form-horizontal" method="post">
            <div class="col-xs-8 col-xs-offset-4">
                <h2>Sign Up</h2>
            </div>    
            <div class="form-group">
                <label class="control-label col-xs-4">Email</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" name="uname" required="required">
                    <span><?php echo $username_err;?></span><br>
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
                <div class="col-xs-8 col-xs-offset-4">
                    <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                </div>  
            </div>	
        </form>
        <div class="text-center">Don't have an account? <a href="signup.html">SignUp here</a></div>
    </main>

</body>
</html>
