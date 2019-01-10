<?php
// Include config file
require_once 'config.php';
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $fullname= $email = $phone = $carlicense = $carmodel = $carcolour = $carseats = "";
$username_err = $password_err = $confirm_password_err = $fullname_err = $email_err = $phone_err = $carlicense_err = $carmodel_err = $carcolour_err = $carseats_err = "";
$gotcar = true;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        // Use prepare statements to prevent sql injection
        $sql_retrieve = "SELECT userid FROM person WHERE username = $1";
        $prepare_retrieve = pg_prepare($db, "", $sql_retrieve);
        
        if ($prepare_retrieve) {
            $execute_retrieve = pg_execute($db, "", array($_POST["username"]));

            $rows = pg_num_rows($execute_retrieve);

            if ($rows >= 1) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
        }

    }
    
    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';     
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }

    //Validate fullname
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Please enter your fullname. ";
    }else{
        $fullname = trim($_POST["fullname"]);
    }

    //Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email. ";
    }else{
        $sql_retrieve = "SELECT userid FROM person WHERE email = $1";
        $prepare_retrieve = pg_prepare($db, "", $sql_retrieve);
        
        if ($prepare_retrieve) {
            $execute_retrieve = pg_execute($db, "", array($_POST["email"]));

            $rows = pg_num_rows($execute_retrieve);

            if ($rows >= 1) {
                $email_err = "This username is already taken.";
            } else {
                $email = trim($_POST["username"]);
            }
        }
    }
    
    //Validate phone
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter your phone. ";
    }else{
        $sql_retrieve = "SELECT userid FROM person WHERE phone = $1";
        $prepare_retrieve = pg_prepare($db, "", $sql_retrieve);
        
        if ($prepare_retrieve) {
            $execute_retrieve = pg_execute($db, "", array($_POST["phone"]));

            $rows = pg_num_rows($execute_retrieve);

            if ($rows >= 1) {
                $phone_err = "This phone number is already in use.";
            } else {
                $phone = trim($_POST["phone"]);
            }
        }
    }

    //Car input validation
    if(empty(trim($_POST["carlicense"]))){
        $carlicense_err = "Please enter a car license. ";
    } else {
        // Use prepare statements to prevent sql injection
        $sql_retrieve = "SELECT license FROM car WHERE license = $1";
        $prepare_retrieve = pg_prepare($db, "", $sql_retrieve);
        
        if ($prepare_retrieve) {
            $execute_retrieve = pg_execute($db, "", array(trim($_POST["carlicense"])));

            $rows = pg_num_rows($execute_retrieve);

            if ($rows >= 1) {
                $carlicense_err = "This car license is already registered.";
            } else {
                $carlicense = trim($_POST["carlicense"]);
            }
        }
    }

    if(empty(trim($_POST["carmodel"]))){
        $carmodel_err = "Please enter a car model. ";
    } else {
        $carmodel = trim($_POST["carmodel"]);
    }

    if(empty(trim($_POST["carcolour"]))){
        $carcolour_err = "Please enter a car colour. ";
    } else {
        $carcolour = trim($_POST["carcolour"]);
    }

    if(empty(trim($_POST["carseats"]))){
        $carseats_err = "Please choose number of car seats. ";
    } else {
        $carseats = trim($_POST["carseats"]);
    }

    if (empty(trim($_POST["carseats"])) && empty(trim($_POST["carcolour"])) && empty(trim($_POST["carmodel"]))
        && empty(trim($_POST["carlicense"]))) {
        $carlicense_err = $carmodel_err = $carcolour_err = $carseats_err = "";
        $gotcar = false;
    }
    
    // Check username input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($fullname_err) && empty($email_err) && empty($phone_err) && empty($confirm_password_err)){
        $sql_update_person = "INSERT INTO person (username, password, fullname, email, phone) 
                        VALUES ($1, $2, $3, $4, $5)";
        //$password_hash = password_hash($password, PASSWORD_DEFAULT);
        $prepare_update_person = pg_prepare($db, "", $sql_update_person);
    }

    //Check car input errors
    if(empty($carlicense_err) && empty($carmodel_err) && empty($carcolour_err) && empty($carseats_err)
        && $gotcar == true) {
        $sql_update_car = "INSERT INTO car (license, model, colour, seats) VALUES ($1, $2, $3, $4)";
        $prepare_update_car = pg_prepare($db, "", $sql_update_car);
    }

    //Executing queries and validations
    if ($prepare_update_person && $gotcar == true && empty($carlicense_err) && empty($carmodel_err) 
                                && empty($carcolour_err) && empty($carseats_err)) {        
        if ($prepare_update_car) {
            $execute_update_car = pg_execute($db, "", array($carlicense, $carmodel, 
                                                        $carcolour, $carseats));
            if (!$execute_update_car) {
                echo "Update car failed!!";
            } else {
                echo "Update car successful!";                
            }
        }
        $sql_update_person = "INSERT INTO person (username, password, fullname, email, phone) 
                        VALUES ($1, $2, $3, $4, $5)";
        $prepare_update_person = pg_prepare($db, "", $sql_update_person);
        if ($prepare_update_person) {
            $execute_update_person = pg_execute($db, "", array($username, $password, 
                                                                $fullname, $email, $phone));
            if (!$execute_update_person) {
                echo "Update person failed!!";
            } else {
                echo "Update person successful!";
                $sql_userid = "SELECT userid FROM person WHERE username = $1";
                $prepare_userid = pg_prepare($db, "", $sql_userid);
                if ($prepare_userid) {
                    $execute_get_userid = pg_execute($db, "", array($username));
                    if ($execute_get_userid) {
                        $rows = pg_fetch_assoc($execute_get_userid);
                        $userid = $rows[userid];
                    }
                }
                $sql_carid = "SELECT carid FROM car WHERE license = $1";
                $prepare_carid = pg_prepare($db, "", $sql_carid);
                if ($prepare_carid) {
                    $execute_get_carid = pg_execute($db, "", array($carlicense));
                    if ($execute_get_carid) {
                        $rows = pg_fetch_assoc($execute_get_carid);
                        $carid = $rows[carid];
                    }
                }

                $sql_usercarid = "INSERT INTO car_ownedby(ownerid, carid) VALUES($1, $2)";
                $prepare_usercarid = pg_prepare($db, "", $sql_usercarid);

                if ($prepare_usercarid) {
                    $execute_insert_usercarid = pg_execute($db, "", array($userid, $carid));
                    if ($execute_insert_usercarid) {
                        "Update successful!";
                    }
                }
                header("location: login.php");                
            }
        }
        
    } else if ($prepare_update_person && $gotcar == false) {
        $execute_update_person = pg_execute($db, "", array($username, $password, 
                                                            $fullname, $email, $phone));
        if (!$execute_update_person) {
            echo "Update person failed!!";
        } else {
            echo "Update person successful!";
            header("location: login.php");               
        }
    } else {
        ;
    }
    

    // Close connection
    pg_close($db);
    
    
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($fullname_err)) ? 'has-error' : ''; ?>">
                <label>Enter your full name </label>
                <input type="text" name="fullname" class="form-control" value="<?php echo $fullname; ?>">
                <span class="help-block"><?php echo $fullname_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email </label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone </label>
                <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                <span class="help-block"><?php echo $phone_err; ?></span>
            </div>
            <h2>Car Registration</h2>
            <p>Register a car if you plan to sign up as a driver (You can leave these fields empty).</p>
            <div class="form-group <?php echo (!empty($carlicense_err)) ? 'has-error' : ''; ?>">
                <label>Car License Plate </label>
                <input type="text" name="carlicense" class="form-control" value="<?php echo $carlicense; ?>">
                <span class="help-block"><?php echo $carlicense_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($carmodel_err)) ? 'has-error' : ''; ?>">
                <label>Car Model </label>
                <input type="text" name="carmodel" class="form-control" value="<?php echo $carmodel; ?>">
                <span class="help-block"><?php echo $carmodel_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($carcolour_err)) ? 'has-error' : ''; ?>">
                <label>Car Colour </label>
                <input type="text" name="carcolour" class="form-control" value="<?php echo $carcolour; ?>">
                <span class="help-block"><?php echo $carcolour_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($carseats_err)) ? 'has-error' : ''; ?>">
                <label>Number of Seats </label>
                <select name="carseats" class="form-control" value="<?php echo $carseats; ?>">
                    <option placeholder="Select number of seats"></option>
                    <option value="2-seater">2-seater</option>
                    <option value="5-seater">5-seater</option>
                    <option value="7-seater">7-seater</option>
                </select>
                <span class="help-block"><?php echo $carseats_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>