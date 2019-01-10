<?php
// Include config file
require_once '../config.php';

// Initialize the session
session_start();

// Set the default timezone to use
date_default_timezone_set('Asia/Singapore');
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['userid']) || empty($_SESSION['userid']) 
  || !isset($_SESSION['isadmin']) || empty($_SESSION['isadmin'])) {
  header("location: ../login.php");
  exit;
} else {
	$user_cars_err = "";
	$user_cars_sql = "SELECT * FROM car NATURAL JOIN car_ownedby WHERE ownerid = $1";
	$prepare_user_cars = pg_prepare($db, "", $user_cars_sql);

	if ($prepare_user_cars) {
		$execute_user_cars = pg_execute($db, "", array($_SESSION['userid']));
		if (!$execute_user_cars) {
			echo "Cannot get your cars!";
		}
	} else {
		echo "Cannot retrieve your cars!";
	}
	
	
}
?>
 
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
	table {
    	width:95%;
	}
	table, th, td {
	    border: 1px solid black;
	    border-collapse: collapse;
	}
	th, td {
	    padding: 5px;
	    text-align: left;
	}
	table#t01 tr:nth-child(even) {
	    background-color: #eee;
	}
	table#t01 tr:nth-child(odd) {
	   background-color:#fff;
	}
	table#t01 th {
	    background-color: black;
	    color: white;
	    text-align:center;
	}

	h2 {
	    display: block;
	    font-size: 1.5em;
	    margin-top: 0.83em;
	    margin-bottom: 0.83em;
	    margin-left: 0;
	    margin-right: 0;
	    font-weight: bold;
	    text-align: center;
	}

	p {text-align:center;}
	td {text-align:center;}
	</style>
</head>
<body>

<h2>Your Registered Cars</h2>

<table id = "t01" align ="center">
  <tr>
    <th>License</th>
    <th>Model</th>
    <th>Colour</th>
    <th>Number of Seats</th>
  </tr>
  <?php echo (!empty($user_cars_err)) ? $user_cars_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_cars)) { ?>
  <tr>
    <td><?php echo $row[license]; ?></td>
    <td><?php echo $row[model]; ?></td>
    <td><?php echo $row[colour]; ?></td>
    <td><?php echo $row[seats]; ?></td>
  </tr>
  <?php }?>
</table>
<br>
<br>
<p><a href="adminprofile.php" class="btn btn-warning">Go Back To Your Profile</a></p>
<p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>

</body>
</html>