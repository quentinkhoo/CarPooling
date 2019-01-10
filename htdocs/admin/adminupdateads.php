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
	$user_bids_err = $rides_err = "";
	$get_user_bid_records = "SELECT bidderid, advertiserid, origin, dest, pickuptime, minbid, fullname, username, bidamt, phone, status FROM bid INNER JOIN person ON bidderid = userid NATURAL JOIN ride WHERE advertiserid = $1 and status = 'open' ORDER BY  pickuptime ASC, origin, dest";
	$prepare_user_bids = pg_prepare($db, "", $get_user_bid_records);
	
    if ($prepare_user_bids) {
		$execute_user_bids = pg_execute($db, "", array($_SESSION['userid']));
		if (!$execute_user_bids) {
			$user_bids_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_user_bids);
			if ($num_rows == 0) {
				$user_bids_err = "You have not made any advertisements.";
			}				
		}
	}
	else {
		$user_bids_err = "SQL statement cannot be prepared :(";
	}

	$get_rides = "SELECT advertiserid, origin, dest, pickuptime, username, status FROM ride left join person on
	winnerid = userid where advertiserid = $1 ORDER BY status DESC" ;
	$prepare_user_rides = pg_prepare($db, "", $get_rides);
	
    if ($prepare_user_rides) {
		$execute_user_rides = pg_execute($db, "", array($_SESSION['userid']));
		if (!$execute_user_rides) {
			$rides_err = "Something went wrong! Please try again.";
		} else {
			$num_rides_rows = pg_num_rows($execute_user_bids);
			if ($num_rows == 0) {
				$user_bids_err = "There are no bidders who have bidded for your rides.";
			}				
		}
	}
	else {
		$user_bids_err = "SQL statement cannot be prepared :(";
	}

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		if (!$_POST['bidderid']) {
			$origin = $_POST['origin'];
			$dest = $_POST['dest'];
			$pickuptime = $_POST['pickuptime'];
			$advertiserid = $_POST['advertiserid'];

			$sql_update_winner = "UPDATE ride SET status = 'close', winnerid = (select bidderid from bid where advertiserid = $1 and origin = $2 and dest = $3 and pickuptime = $4 and bidamt >= (select max(bidamt) from bid where advertiserid = $1 and origin = $2 and dest = $3 and pickuptime = $4) limit 1) 
			WHERE origin = $2 and dest = $3 and pickuptime = $4 and advertiserid = $1";
			$prepare_update_winner = pg_prepare($db, "", $sql_update_winner);

			if ($prepare_update_winner) {
				$execute_update_winner = pg_execute($db, "", array($advertiserid, $origin, $dest, $pickuptime));

				if ($execute_update_winner) {
					echo header("location: updateads.php");
				} else {
					echo "Failed to select winner.\n Please check that you have a bidder for this ride
							\nEither that or the bidder might have already been selected for another ride
							at the same time.";
				}
			} else {
				echo "Failed to prepare!";
			}
		} else {
			$origin = $_POST['origin'];
			$dest = $_POST['dest'];
			$pickuptime = $_POST['pickuptime'];
			$advertiserid = $_POST['advertiserid'];
			$bidderid = $_POST['bidderid'];

			$sql_update_winner = "UPDATE ride set STATUS = 'close', winnerid = $1
									WHERE advertiserid = $2 and origin = $3 and dest = $4 and pickuptime = $5";
			$prepare_update_winner = pg_prepare($db, "", $sql_update_winner);

			if ($prepare_update_winner) {
				$execute_update_winner = pg_execute($db, "", array($bidderid, $advertiserid, $origin, $dest, $pickuptime));

				if ($execute_update_winner) {
					echo header("location: updateads.php");
				} else {
					echo "The selected bidder cannot go for your ride.";
				}
			} else {
				echo "Failed to prepare!";
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

<h2>Your Advertisements</h2>

<table id = "t01" align ="center">
  <tr>
    <th>Origin</th>
    <th>Destination</th>
    <th>Pickup Time</th>
    <th>Status</th>
    <th>Winner</th>
    <th>Choose For Me</th>
  </tr>
  <?php echo (!empty($rides_err)) ? $rides_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_rides)) { ?>
  <tr>
    <td><?php echo $row[origin]; ?></td>
    <td><?php echo $row[dest]; ?></td>
    <td><?php echo $row[pickuptime]; ?></td>
    <td><?php echo $row[status]; ?></td>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo ($row[status] == 'open') ? 
    '<form action="" method="post">'
    .'<input name="origin" type="hidden" value="'.$row[origin].'" />'
    .'<input name="dest" type="hidden" value="'.$row[dest].'" />'
    .'<input name="pickuptime" type="hidden" value="'.$row[pickuptime].'" />'
    .'<input name="advertiserid" type="hidden" value="'.$row[advertiserid].'" />'
    .'<input type="submit" class="btn btn-primary" value="Automatically Choose Highest Bidder" /></form>'

    : 'You have chosen a winner!'?>
    </td>
  </tr>
  <?php }?>
</table>

<h2>Your Bidders</h2>

<table id = "t01" align ="center">
  <tr>
    <th>Origin</th>
    <th>Destination</th>
    <th>Time</th>
    <th>Minimum Bid</th>
    <th>Bidder's Full Name</th>
    <th>Bidder's Username</th>
    <th>Bidder's Bid</th>
    <th>Bidder's Contact</th>
    <th>Status</th>
    <th>Remarks</th>
  </tr>
  <?php echo (!empty($user_bids_err)) ? $user_bids_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_user_bids)) { ?>
  <tr>
    <td><?php echo $row[origin]; ?></td>
    <td><?php echo $row[dest]; ?></td>
    <td><?php echo $row[pickuptime]; ?></td>
    <td><?php echo $row[minbid]; ?></td>
    <td><?php echo $row[fullname]; ?></td>
    <td><?php echo $row[username]; ?></td>
    <td><?php echo $row[bidamt]; ?></td>
    <td><?php echo $row[phone]; ?></td>
    <td><?php echo $row[status]; ?></td>
    <td>
    	<form action="" method="post">
		    <input name="origin" type="hidden" value="<?php echo $row[origin]; ?>" />
		    <input name="dest" type="hidden" value="<?php echo $row[dest]; ?>" />
		    <input name="pickuptime" type="hidden" value="<?php echo $row[pickuptime]; ?>" />
		    <input name="advertiserid" type="hidden" value="<?php echo $row[advertiserid]; ?>" />
		    <input name="bidderid" type="hidden" value="<?php echo $row[bidderid]; ?>" />
		    <input type="submit" class="btn btn-primary" value="Choose This Bidder As Winner" />
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