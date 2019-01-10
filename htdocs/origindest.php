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
	$origin_count = "SELECT origin, count(*) FROM ride GROUP BY origin ORDER BY count DESC";
	$prepare_origin_count = pg_prepare($db, "", $origin_count);

	if ($prepare_origin_count) {
		$execute_origin_count = pg_execute($db, "", array());

		if ($execute_origin_count) {
			$num_rows = pg_num_rows($execute_origin_count);
			if ($num_rows == 0) {
				$origin_err = "There are no rides yet!";
			}
		}
	}

	$dest_count = "SELECT dest, count(*) FROM ride GROUP BY dest ORDER BY count DESC";
	$prepare_dest_count = pg_prepare($db, "", $dest_count);

	if ($prepare_dest_count) {
		$execute_dest_count = pg_execute($db, "", array());

		if ($execute_dest_count) {
			$num_rows = pg_num_rows($execute_dest_count);
			if ($num_rows == 0) {
				$dest_err = "There are no rides yet!";
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

<H2>Origin</H2>

<table id = "t01" align ="center">
  <tr>
    <th>Origin</th>
    <th>Number of times appeared</th>
  </tr>
  <?php echo (!empty($origin_err)) ? $origin_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_origin_count)) { ?>
  <tr>
    <td><?php echo $row[origin]; ?></td>
    <td><?php echo $row[count]; ?></td>
  </tr>
  <?php }?>
</table>

<H2>Destination</H2>

<table id = "t01" align ="center">
  <tr>
    <th>Destination</th>
    <th>Number of times appeared</th>
  </tr>
  <?php echo (!empty($dest_err)) ? $dest_err : ''; ?>
  <?php while($row = pg_fetch_assoc($execute_dest_count)) { ?>
  <tr>
    <td><?php echo $row[dest]; ?></td>
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