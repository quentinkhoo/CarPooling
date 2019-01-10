<?php
// Include config file
require_once 'config.php';

// Initialize the session
session_start();

// Set the default timezone to use
date_default_timezone_set('Asia/Singapore');

if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])){
  header("location: login.php");
  exit;
} else {
	$user_bids = "SELECT username, count(*) FROM ride INNER JOIN person ON winnerid = userid
					GROUP BY username ORDER BY count desc";
	$prepare_user_bids = pg_prepare($db, "", $user_bids);

	if ($prepare_user_bids) {
		$execute_user_bids = pg_execute($db, "", array());

		if ($execute_user_bids) {
			$num_rows = pg_num_rows($execute_user_bids);
			if ($num_rows == 0) {
				$user_bids_err = "There are no bids made";
			}
		}
	}

	$user_rides = "SELECT username, count(*) FROM ride INNER JOIN person ON advertiserid = userid
					GROUP BY username ORDER BY count desc";
	$prepare_user_rides = pg_prepare($db, "", $user_rides);

	if ($prepare_user_rides) {
		$execute_user_rides = pg_execute($db, "", array());

		if ($execute_user_rides) {
			$num_rows = pg_num_rows($execute_user_rides);
			if ($num_rows == 0) {
				$user_rides_err = "There are no rides yet!";
			}
		}
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
    	width:40%;
    	float:center;
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

<H2>User Bid Statistics</H2>

<table id = "t01" align ="center">
  <tr>
    <th>Username</th>
    <th>Number of Bids Won</th>
  </tr>
  <?php echo (!empty($user_bids_err)) ? $user_bids_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_bids)) { ?>
  <tr>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo $row[count]; ?></td>
  </tr>
  <?php }?>
</table>

<H2>User Advertisement Statistics</H2>

<table id = "t01" align ="center">
  <tr>
    <th>Username</th>
    <th>Number of Advertisements Made</th>
  </tr>
  <?php echo (!empty($user_rides_err)) ? $user_rides_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_rides)) { ?>
  <tr>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo $row[count]; ?></td>
  </tr>
  <?php }?>
</table>
<br>
<br>
	<p><a href="welcome.php" class="btn btn-warning">Go Back</a></p>
	<p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>

</body>
</html>