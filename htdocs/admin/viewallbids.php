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
	$get_bids = "SELECT * FROM bid";
	$prepare_get_bids = pg_prepare($db, "", $get_bids);
	
    if ($prepare_get_bids) {
		$execute_get_bids = pg_execute($db, "", array());
		if (!$execute_get_bids) {
			$bids_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_get_bids);
			if ($num_rows == 0) {
				$bids_err = "There are no advertisements available.";
			}				
		}
	} else {
		$adverts_err = "SQL statement cannot be prepared :(";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {

		$bidamt = $_POST['bidamt'];
		$bidtime = $_POST['bidtime'];
		$bidderid = $_POST['bidderid'];
		$advertiserid = $_POST['advertiserid'];
		$origin = $_POST['origin'];
		$dest = $_POST['dest'];
		$pickuptime = $_POST['pickuptime'];
		$minbid = $_POST['minbid'];
		$updatedelete = $_POST['updatedelete'];
		
		if ($updatedelete == 'Update') {
			$update_bid_sql = "UPDATE bid SET bidamt = $1, bidtime = $2, bidderid = $3, advertiserid = $4, 
								origin = $5, dest = $6, pickuptime = $7, minbid = $8
								WHERE origin = $5 AND dest = $6 AND pickuptime = $7 AND advertiserid = $4
								AND bidderid = $3";
								
			$prepare_update_bid = pg_prepare($db, "", $update_bid_sql);


			if ($prepare_update_bid) {
				$execute_update_bid = pg_execute($db, "", array($bidamt, $bidtime, $bidderid, $advertiserid, $origin,
																$dest, $pickuptime, $minbid));
				if ($execute_update_bid) {
					echo "Update was a success!";
				} else {
					echo "Update failed! Please check that your entries are valid!";
				}
			} else {
				echo "Fail preparing!";
			}
		}

		else if ($updatedelete == 'Delete') {
			$delete_bid_sql = "DELETE FROM bid
								WHERE origin = $1 AND dest = $2 AND pickuptime = $3 AND advertiserid = $4
								AND bidderid = $5";
								
			$prepare_delete_bid = pg_prepare($db, "", $delete_bid_sql);


			if ($prepare_delete_bid) {
				$execute_delete_bid = pg_execute($db, "", array($origin, $dest, $pickuptime, $advertiserid,
																$bidderid));
				if ($execute_delete_bid) {
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
        <h1><b>List of Bids</b></h1>
		<table style="width:100%">
		<tr>
			<th><p>Bid Amount</p></th>
			<th><p>Bid Time</p></th>
			<th><p>Bidder ID</p></th>
			<th><p>Advertiser ID</p></th>
			<th><p>Origin</p></th>
			<th><p>Destination</p></th>
			<th><p>Pick Up Time</p></th>
			<th><p>Minimum Bid</p></th>
			<th><p>Update</p></th>
			<th><p>Delete</p></th>
		</tr>
		<?php echo (!empty($bids_err)) ? $bids_err : ''; ?>
		<?php while($row = pg_fetch_assoc($execute_get_bids)) { ?>
			<tr>
			<form action="" method="post">
			<td>
				<div class="form-group" align="center">
                <input type="text" name="bidamt" class="form-control" value="<?php echo $row[bidamt]; ?>">
	            </div>
	        </td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="bidtime" class="form-control" value="<?php echo $row[bidtime]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="bidderid" class="form-control" value="<?php echo $row[bidderid]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="advertiserid" class="form-control" value="<?php echo $row[advertiserid]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="origin" class="form-control" value="<?php echo $row[origin]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="dest" class="form-control" value="<?php echo $row[dest]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="pickuptime" class="form-control" value="<?php echo $row[pickuptime]; ?>">
                <span class="help-block"><?php echo $advertiserid_err; ?></span>
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="minbid" class="form-control" value="<?php echo $row[minbid]; ?>">
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