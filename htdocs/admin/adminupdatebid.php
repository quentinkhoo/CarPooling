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
	$user_bid_err = $user_bid_success = "";
	$get_user_bid_records = "SELECT * FROM bid INNER JOIN person ON userid = advertiserid NATURAL JOIN ride WHERE bidderid = $1 AND status = $2" ;
	$prepare_user_bids = pg_prepare($db, "", $get_user_bid_records);
	
    if ($prepare_user_bids) {
		$execute_user_bids = pg_execute($db, "", array($_SESSION['userid'], 'open'));
		if (!$execute_user_bids) {
			$user_bid_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_user_bids);
			if ($num_rows == 0) {
				$user_bid_err = "You have not bidded for any rides!";
			}				
		}
	}
	else {
		$user_bid_err = "SQL statement cannot be prepared :(";
	}

	if($_SERVER["REQUEST_METHOD"] == "POST") {

		$pickuptime = $_POST["pickuptime"];
		$advertiserid = $_POST["advertiserid"];
		$origin = trim($_POST["origin"]);
		$dest = trim($_POST["dest"]);
		$bidderid = $_SESSION["userid"];
		$bidtime = trim($_POST["bidtime"]);
		$usernewbid = trim($_POST["newbid"]);
		$minbid = substr($_POST["minbid"], 1);

		if (empty(trim($_POST["newbid"]))) {
			$user_bid_err = "Please input a bid amount";
		} else if (!is_numeric(trim($_POST['newbid']))) {
			$user_bid_err = "Only numeric input is allowed.";
		} else if ($usernewbid < $minbid) {
			$user_bid_err = 'You are not bidding enough. Minimum bid is ' . trim($_POST["minbid"]);
		} else {

			$update_bid = "UPDATE bid SET bidamt = $1, bidtime = $2
									WHERE pickuptime = $3 AND origin = $4 AND dest = $5 AND advertiserid = $6
									AND bidderid = $7";
			$prepare_update_bid = pg_prepare($db, "", $update_bid);
			if ($prepare_update_bid) {
				$execute_update_bid = pg_execute($db, "", array($usernewbid, $bidtime, $pickuptime, $origin, $dest, $advertiserid, $bidderid));
				if ($execute_update_bid) {
					$user_bid_success = "You have succesfully updated your bid!";
				} else {
					$user_bid_error = "Couldn't update bid!";
				}
			} else {
				$user_bid_error = "Cannot update bid!";
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

<h2>List of Bids</h2>

<table id = "t01" align ="center">
  <tr>
    <th>Driver Username</th>
    <th>Driver Contact</th>
    <th>Origin</th>
    <th>Destination</th>
    <th>Time</th>
    <th>Minimum Bid</th>
    <th>Your Bid</th>
  </tr>
  <?php while($row = pg_fetch_assoc($execute_user_bids)) { ?>
  <tr>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo $row[phone]; ?></td>
    <td><?php echo $row[origin]; ?></td>
    <td><?php echo $row[dest]; ?></td>
    <td><?php echo $row[pickuptime]; ?></td>
    <td><?php echo $row[minbid]; ?></td>
    <?php $bidamt = substr($row[bidamt], 1);?>
    <td>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($user_bid_err) && $row[advertiserid] == $advertiserid && $row[pickuptime] == $pickuptime && $row[origin] == $origin && $row[dest] == $dest) ? 'has-error' : ''; ?>">					
				<input type="submit" value="Update Bid" class="btn btn-primary" style="float: right" />
					<div style="overflow: hidden; padding-right: .25em;">
					<input type="number" name="newbid" value="<?php echo (!empty($user_bid_success && $row[advertiserid] == $advertiserid && $row[pickuptime] == $pickuptime && $row[origin] == $origin && $row[dest] == $dest) ? $usernewbid : $bidamt); ?>" class="form-control" style="width: 100%;" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100"/>
					<span class="help-block"><?php echo ($row[advertiserid] == $advertiserid && $row[pickuptime] == $pickuptime && $row[origin] == $origin && $row[dest] == $dest) ? $user_bid_err.$user_bid_success : ""; ?></span>
					</div>
			</div>
			<input name="origin" type="hidden" value="<?php echo $row[origin]; ?>" />
			<input name="dest" type="hidden" value="<?php echo $row[dest]; ?>" />
			<input name="pickuptime" type="hidden" value="<?php echo $row[pickuptime]; ?>" />
			<input name="advertiserid" type="hidden" value="<?php echo $row[advertiserid]; ?>" />
			<input name="minbid" type="hidden" value="<?php echo $row[minbid]; ?>" />
			<input name="bidderid" type="hidden" value="<?php echo $_SESSION['userid']; ?>" />
			<input name="bidtime" type="hidden" value="<?php echo date("Y-m-d H:i:s") ?>" />
		</form>
	</td>
  </tr>
  <?php }?>
</table>
<br>
<br>
<p><a href="adminwelcome.php" class="btn btn-warning">Go Back</a></p>
<p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>

</body>
</html>