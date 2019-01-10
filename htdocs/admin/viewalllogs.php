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
	$get_logAccount = "SELECT * FROM logAccount";
	$prepare_logAccount = pg_prepare($db, "", $get_logAccount);
	
    if ($prepare_logAccount) {
		$execute_logAccount = pg_execute($db, "", array());
		if (!$execute_logAccount) {
			$logAccount_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_logAccount);
			if ($num_rows == 0) {
				$logAccount_err = "There are no logs available.";
			}				
		}
	} else {
		$logAccount_err = "SQL statement cannot be prepared :(";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {

		$logID = $_POST['logid'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		$fullname = $_POST['fullname'];
		$phone = $_POST['phone'];
		$action = $_POST['action'];
		
		$update_logAccount_sql = "UPDATE logAccount SET logid = $1, username = $2, password = $3, email = $4, fullname = $5,
							phone = $6, action = $7
							WHERE logID = $1";
							
		$prepare_update_logAccount = pg_prepare($db, "", $update_logAccount_sql);


		if ($prepare_update_logAccount) {
			$execute_update_logAccount = pg_execute($db, "", array($logid, $username, $password, $email, $fullname, $phone, $action));
			if ($execute_update_logAccount) {
				echo "Update was a success!";
			} else {
				echo "Update failed! Please check that your entries are valid!";
			}
		} else {
			echo "Fail preparing!";
		}
	}
	
	$get_adverts_record = "SELECT * FROM logride";
	$prepare_adverts = pg_prepare($db, "", $get_adverts_record);
	
    if ($prepare_adverts) {
		$execute_adverts = pg_execute($db, "", array());
		if (!$execute_adverts) {
			$adverts_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_adverts);
			if ($num_rows == 0) {
				$adverts_err = "There are no advertisements available.";
			}				
		}
	} else {
		$adverts_err = "SQL statement cannot be prepared :(";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {

		$origin = $_POST['origin'];
		$dest = $_POST['dest'];
		$pickuptime = $_POST['pickuptime'];
		$minbid = $_POST['minbid'];
		$status = $_POST['status'];
		$carid = $_POST['carid'];
		$advertiserid = $_POST['advertiserid'];
		$winnerid = $_POST['winnerid'];
		$action = $_POST['action'];
		if (!$winnerid) {
			$winnerid = NULL;
		}
		
		$update_ride_sql = "UPDATE ride SET origin = $1, dest = $2, pickuptime = $3, minbid = $4, status = $5,
							carid = $6, advertiserid = $7, winnerid = $8, action = $9
							WHERE origin = $1 AND dest = $2 AND pickuptime = $3 AND advertiserid = $7";
							
		$prepare_update_ride = pg_prepare($db, "", $update_ride_sql);


		if ($prepare_update_ride) {
			$execute_update_ride = pg_execute($db, "", array($origin, $dest, $pickuptime, $minbid, $status,
															$carid, $advertiserid, $winnerid, $action));
			if ($execute_update_ride) {
				echo "Update was a success!";
			} else {
				echo "Update failed! Please check that your entries are valid!";
			}
		} else {
			echo "Fail preparing!";
		}
	}
	
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Logs</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        body { 
        	font: 14px sans-serif; 
        	text-align: center; 
        }

		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			table-layout: auto;
			width: 80px;
		}

		th, td {
			padding: 10px;
			text-align: center;
		}

		p {
			text-align: center;
		}
    </style>
</head>
<body>
    <div class="page-header">
        <h1><b>Account Logs</b></h1>
		<table style="width:100%">
			<tr>
				<th><p>Log ID</p></th>
				<th><p>Username</p></th>
				<th><p>Password</p></th>
				<th><p>Email</p></th>
				<th><p>Fullname</p></th>
				<th><p>Phone</p></th>
				<th><p>Action</p></th>
			</tr>
			<?php echo (!empty($logAccount_err)) ? $logAccount_err : ''; ?>
			<?php while($row = pg_fetch_assoc($execute_logAccount)) { ?>
				<tr>
					<td><?php echo $row[logid]; ?></td>
					<td><?php echo $row[username]; ?></td>
					<td><?php echo $row[password]; ?></td>
					<td><?php echo $row[email]; ?></td>
					<td><?php echo $row[fullname]; ?></td>
					<td><?php echo $row[phone]; ?></td>
					<td><?php echo $row[action]; ?></td>
				</tr>
			<?php } ?>
		</table>
		
		<h1><b>Advertisement Logs</b></h1>
		<table style="width:100%">
			<tr>
				<th><p>Origin</p></th>
				<th><p>Destination</p></th>
				<th><p>Pick Up Time</p></th>
				<th><p>Minimum Bid</p></th>
				<th><p>Status</p></th>
				<th><p>Car ID</p></th>
				<th><p>Advertiser ID</p></th>
				<th><p>Winner ID</p></th>
				<th><p>Action</p></th>
			</tr>
			<?php echo (!empty($adverts_err)) ? $adverts_err : ''; ?>
			<?php while($row = pg_fetch_assoc($execute_adverts)) { ?>
				<tr>
					<td><?php echo $row[origin]; ?></td>
					<td><?php echo $row[dest]; ?></td>
					<td><?php echo $row[pickuptime]; ?></td>
					<td><?php echo $row[minbid]; ?></td>
					<td><?php echo $row[status]; ?></td>
					<td><?php echo $row[carid]; ?></td>
					<td><?php echo $row[advertiserid]; ?></td>
					<td><?php echo $row[winnerid]; ?></td>
					<td><?php echo $row[action]; ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php echo "Data retrieved on:" ?>
		<?php echo date("Y-m-d H:i:s") ?>
    </div>
    <p><a href="adminwelcome.php" class="btn btn-warning">Go Back</a></p>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
</body>
</html>