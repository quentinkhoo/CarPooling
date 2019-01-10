<?php
// Include config file
require_once 'config.php';

//Check if login sesssion is set
session_start();
if(isset($_SESSION['username'])){
	header("location: welcome.php");

} else {
 
	// Define variables and initialize with empty values
	$username = $password = "";
	$username_err = $password_err = "";
	 
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	 
	    // Check if username is empty
	    if(empty(trim($_POST["username"]))){
	        $username_err = 'Please enter username.';
	    } else{
	        $username = trim($_POST["username"]);
	    }
	    
	    // Check if password is empty
	    if(empty(trim($_POST['password']))){
	        $password_err = 'Please enter your password.';
	    } else{
	        $password = trim($_POST['password']);
	    }
	    
	    // Validate credentials
	    if(empty($username_err) && empty($password_err)){
	    	$get_user_record = "SELECT username, password, userid, isadmin FROM person WHERE username = $1";
	    	$prepare_login = pg_prepare($db, "", $get_user_record);
	        if ($prepare_login) {
	        	$execute_login = pg_execute($db, "", array($username));
		        if (!$execute_login) {
		        	echo "Something went wrong! Please try again.";
		        } else {
		        	$num_rows = pg_num_rows($execute_login);
		        	$user_details = pg_fetch_assoc($execute_login);
		        	if ($num_rows == 1) {
		        		$hashed_password = ($user_details[password]);

			        	//Check if password is correct and start a new session if so
			        	if (md5($password) == $hashed_password) {
			    			session_start();
			    			$_SESSION['userid'] = $user_details[userid];
			    			if ($user_details[isadmin] ==  't') {
			    				$_SESSION['isadmin'] = $user_details['isadmin'];
			    				header("location: admin/adminwelcome.php");
			    			} else {
				    			header("location: welcome.php");
				    		}
			        	} else {
			        		$password_err = "You have entered an invalid password";
	    				}
		        	} else {
		        		$username_err = "You have entered an invalid username";
		        	}
		        	
		        }
	        }
	        
	        
	    }

	}
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>    
</body>
</html>