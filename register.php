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

    function filter_email($field){
        //Sanitize email
        $field - filter_var(trim($field),FILTER_SANITIZE_EMAIL);

        //Validate email
        if(filter_var($field, FILTER_VALIDATE_EMAIL)){
            return $field;
        }else{
            return FALSE;
        }
    }
    
    function filter_uid($field){
        //Sanitize email
        $field - filter_var(trim($field),FILTER_SANITIZE_INT);

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

    $email = $password = $r_password = $id = $fname = $lname = "";
    $email_err = $password_err = $r_password_err = $id_err = $fname_err = $lname_err = "";

    #Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
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

        // Validate email
        if(empty($_POST["email"])){
            $email_err = "Please enter your email.";
        } else{
            $email = filter_email($_POST["email"]);
            if($email == FALSE){
                $email_err = "Please enter a valid email.";
            }
        }

        // Validate id
        if(empty($_POST["id"])){
            $id_err = "Please enter your MobilityPLUS ID.";
        } else{
            $id = filter_id($_POST["id"]);
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
                $password_err = "Please enter a valid password.";
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
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>

    <link href="reset.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    
    <header>
        <img src="images/grt-logo.PNG">
        <h1 class="title">MobilityPLUS Online Booking</h1>
    </header>

    <main class="sign_up_main">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" class="signInForm" method="post">
            <div class="container">
                <h1>Sign Up</h1>
                <p>Please fill in this form to create an account.</p>
                <hr>
                
                <label for="fname"><b>First Name</b></label>
                <input type="text" placeholder="Enter First Name" name="fname"><br>
                <span><?php echo $fname_err;?></span><br>

                <label for="lname"><b>Last Name</b></label>
                <input type="text" placeholder="Enter Last Name" name="lname"><br>
                <span><?php echo $lname_err;?></span><br>

                <label for="id"><b>Mobility PLUS ID</b></label>
                <input type="text" placeholder="Enter Mobility PLUS ID" name="id"> <br>
                <span><?php echo $id_err;?></span><br>
            
                <label for="email"><b>Email</b></label><br>
                <input type="text" placeholder="Enter Email" name="email"><br>
                <span><?php echo $email_err;?></span><br>
            
                <label for="pwd"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="pwd"><br>
                <span><?php echo $password_err;?></span><br>
            
                <label for="r_pwd"><b>Repeat Password</b></label>
                <input type="password" placeholder="Repeat Password" name="r_pwd"><br>
                <span><?php echo $r_password_err;?></span><br>
            
                <label>
                    <input class="agreeCheckBox"  type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
                </label>
            
                <p>By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>
            
                <div class="container_bot">
                    <button type="button" class="cancelbtn">Cancel</button>
                    <button type="submit" class="signupbtn">Sign Up</button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>
