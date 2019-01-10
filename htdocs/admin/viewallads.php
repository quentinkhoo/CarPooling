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
	$get_adverts_record = "SELECT * FROM ride";
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
		if (!$winnerid) {
			$winnerid = NULL;
		}
		
		if ($updatedelete = 'Update') {
			$update_ride_sql = "UPDATE ride SET origin = $1, dest = $2, pickuptime = $3, minbid = $4, status = $5,
								carid = $6, advertiserid = $7, winnerid = $8
								WHERE origin = $1 AND dest = $2 AND pickuptime = $3 AND advertiserid = $7";
								
			$prepare_update_ride = pg_prepare($db, "", $update_ride_sql);


			if ($prepare_update_ride) {
				$execute_update_ride = pg_execute($db, "", array($origin, $dest, $pickuptime, $minbid, $status,
																$carid, $advertiserid, $winnerid));
				if ($execute_update_ride) {
					echo "Update was a success!";
				} else {
					echo "Update failed! Please check that your entries are valid!";
				}
			} else {
				echo "Fail preparing!";
			}
		} else if ($updatedelete = 'Delete') {
			$delete_ride_sql = "DELETE FROM ride
								WHERE origin = $1 AND dest = $2 AND pickuptime = $3 AND advertiserid = $4";
								
			$prepare_delete_ride = pg_prepare($db, "", $delete_ride_sql);


			if ($prepare_delete_ride) {
				$execute_delete_ride = pg_execute($db, "", array($origin, $dest, $pickuptime, $advertiserid));
				if ($execute_delete_ride) {
					echo "Delete was a success!";
				} else {
					echo "Delete failed! Please ensure that you have not unnecessarily changed the values!";
				}
			} else {
				echo "Fail preparing!";
			}
		}
	}
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			table-layout: fixed;
			width: 80px;
		}
		th, td {
			padding: 10px;
		}
		
		input[type="text"] {
 		   width: 80px;
 		   height: 30px;
		}
		
		p {text-align:center;}
		td {text-align:center;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1><b>List of Rides</b></h1>
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
			<th><p>Update</p></th>
			<th><p>Delete</p></th>
		</tr>
		<?php echo (!empty($adverts_err)) ? $adverts_err : ''; ?>
		<?php while($row = pg_fetch_assoc($execute_adverts)) { ?>
			<tr>
			<form action="" method="post">
			<td>
				<div class="form-group" align="center">
                <input type="text" name="origin" class="form-control" value="<?php echo $row[origin]; ?>">
                <span class="help-block"><?php echo $origin_err; ?></span>
	            </div>
	        </td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="dest" class="form-control" value="<?php echo $row[dest]; ?>">
                <span class="help-block"><?php echo $dest_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="pickuptime" class="form-control" value="<?php echo $row[pickuptime]; ?>">
                <span class="help-block"><?php echo $pickuptime_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="minbid" class="form-control" value="<?php echo $row[minbid]; ?>">
                <span class="help-block"><?php echo $minbid_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="status" class="form-control" value="<?php echo $row[status]; ?>">
                <span class="help-block"><?php echo $status_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="carid" class="form-control" value="<?php echo $row[carid]; ?>">
                <span class="help-block"><?php echo $carid_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="advertiserid" class="form-control" value="<?php echo $row[advertiserid]; ?>">
                <span class="help-block"><?php echo $advertiserid_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="winnerid" class="form-control" value="<?php echo $row[winnerid]; ?>">
                <span class="help-block"><?php echo $winnerid_err; ?></span>
	            </div>
			</td>
			<td>
				<input type="submit" name = "updatedelete" class="btn btn-primary" value="Update" />
			</td>
			<td>
				<input type="submit" name = "updatedelete" class="btn btn-primary" value="Delete" />
			</td>
			</form>
			</tr>
		<?php } ?>
		</table>
		<?php echo date("Y-m-d H:i:s") ?>
    </div>
    <p><a href="adminwelcome.php" class="btn btn-warning">Go Back</a></p>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
</body>
</html>