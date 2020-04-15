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
                                header("location: dashboard.php");
                            }
                            else{
                                //display error statement for invalid password
                                $password_err = "Password is NOT VALID";
                            }
                        }
                    }
                    else{
                        //display error stament for invalid username
                        $username_err = "Username is NOT VALID";
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
</head>
<body>
    
    <header>
        <img src="images/grt-logo.PNG">
        <h1 class="title">MobilityPLUS Online Booking</h1>
    </header>

    <main class="sign_in_main">
        <h2>Sign In</h2>
        <h2 class="sign_up"><a href="signup.html">Sign Up</a></h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="signInForm" method="post">
            <div class="container" <?php echo (!empty($username_err))?'has-error' : '';?>>
                <label for="uname"><b>Username</b></label><br>
                <input type="text" placeholder="Enter Username" name="uname" value="<?php echo $username;?>"><br>
                <span><?php echo $username_err;?></span><br>

                <label for="pwd"><b>Password</b></label><br>
                <input type="password" placeholder="Enter Password" name="pwd" value="<?php echo $password;?>"><br>
                <span><?php echo $password_err;?></span><br>
                    
                <button type="submit">Login</button>
            </div>

            <div class="container_bot">
                <button type="button" class="cancelbtn">Cancel</button>
                <span class="psw">Forgot <a href="#">password?</a></span>
            </div>

        </form>
    </main>

</body>
</html>
