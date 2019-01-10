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
	$get_users = "SELECT userid, username, email, phone, fullname, isadmin FROM person";
	$prepare_users = pg_prepare($db, "", $get_users);
	
    if ($prepare_users) {
		$execute_users = pg_execute($db, "", array());
		if (!$execute_users) {
			$users_err = "Something went wrong! Please try again.";
		} else {
			$num_rows = pg_num_rows($execute_users);
			if ($num_rows == 0) {
				$users_err = "There are no advertisements available.";
			}				
		}
	} else {
		$users_err = "SQL statement cannot be prepared :(";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = $_POST['username'];
		$email = $_POST['email'];
		$fullname = $_POST['fullname'];
		$phone = $_POST['phone'];
		$isadmin = $_POST['isadmin'];
		$userid = $_POST['userid'];
		$updatedelete = $_POST['updatedelete'];

		if ($updatedelete == 'Update') {
			$update_user = "UPDATE person SET username = $1, email = $2, fullname = $3, phone = $4, isadmin = $5
							WHERE userid = $6";
			$prepare_update_user = pg_prepare($db, "", $update_user);

			if ($prepare_update_user) {
				$execute_update_user = pg_execute($db, "", array($username, $email, $fullname, $phone,
											$isadmin, $userid));
				if ($execute_update_user) {
					echo "User update success!";
				} else {
					echo "Cannot update user :(";
				}
			} else {
				echo "Cannot prepare statement :(";
			}
		} else if ($updatedelete == 'Delete') {
			$delete_user_sql = "DELETE FROM person WHERE userid = $1";
								
			$prepare_delete_user = pg_prepare($db, "", $delete_user_sql);


			if ($prepare_delete_user) {
				$execute_delete_user = pg_execute($db, "", array($userid));
				if ($execute_delete_user) {
					echo "Delete was a success!";
				} else {
					echo "Delete failed! Please check that this user has no active bids or rides.";
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
 		   width: 150px;
 		   height: 30px;
		}
		
		p {text-align:center;}
		td {text-align:center;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1><b>List of Users</b></h1>
		<table style="width:100%">
		<tr>
			<th><p>User ID</p></th>
			<th><p>Username</p></th>
			<th><p>Email</p></th>
			<th><p>Phone</p></th>
			<th><p>Full Name</p></th>
			<th><p>Is Administrator</p></th>
			<th><p>Update</p></th>
			<th><p>Delete</p></th>
		</tr>
		<?php echo (!empty($execute_users)) ? $users_err : ''; ?>
		<?php while($row = pg_fetch_assoc($execute_users)) { ?>
			<tr>
			<form action="" method="post">
			<td>
				<div class="form-group" align="center">
                <input type="text" name="userid" class="form-control" value="<?php echo $row[userid]; ?>">
	            </div>
	        </td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="username" class="form-control" value="<?php echo $row[username]; ?>">
	            </div>
	        </td>
			<td>
				<div class="form-group" align="center" width="80px">
                <input type="text" name="email" class="form-control" value="<?php echo $row[email]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="phone" class="form-control" value="<?php echo $row[phone]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="fullname" class="form-control" value="<?php echo $row[fullname]; ?>">
	            </div>
			</td>
			<td>
				<div class="form-group" align="center">
                <input type="text" name="isadmin" class="form-control" value="<?php echo $row[isadmin]; ?>">
	            </div>
			</td>
			<td>
				<input type="submit" name = "updatedelete" class="btn btn-primary" value="Update" />
			</td>
			<td>
				<input type="submit" name = "updatedelete" class="btn btn-primary" value="Delete" />
			</td>
			<input name="userid" type="hidden" value="<?php echo $row[userid]; ?>" />
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