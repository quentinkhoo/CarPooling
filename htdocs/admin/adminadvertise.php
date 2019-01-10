<?php
//NOT TO BE CONFUSED WITH ADVERTISEMENTS.PHP, THIS FILE IS TO CREATE ADVERTISEMENTS

require_once '../config.php';

session_start();

date_default_timezone_set('Asia/Singapore');
 
//Define all the variables I need to use first
$origin = $dest = $pickuptime = $license = "";
$minbid = 10.00;
$origin_err = $dest_err = $pickuptime_err = $minbid_err = $license_err = "";

//Redirect to login page if session has not started
if(!isset($_SESSION['userid']) || empty($_SESSION['userid']) 
  || !isset($_SESSION['isadmin']) || empty($_SESSION['isadmin'])) {
  header("location: ../login.php");
  exit;
} else {
	//Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		//Validate origin
		if(empty(trim($_POST["origin"]))){
        	$origin_err = "Please enter the location you wish to start from. ";
    	} else {
        	$origin = trim($_POST["origin"]);
    	}

    	//Validate destination
    	if(empty(trim($_POST["dest"]))){
        	$dest_err = "Please enter your destination. ";
    	} else {
        	$dest = trim($_POST["dest"]);
    	}

    	//Validate input date
    	if(empty(trim($_POST["pickuptime"]))){
	        $pickuptime_err = "Please choose a date and time. ";
	    } else {
	        $pickuptime = trim($_POST["pickuptime"]);
	    }

        //Validate input mininum bid
        if(empty(trim($_POST["minbid"]))) {
            $minbid_err = "Please input a minimum bid. ";
        } else {
            $minbid = trim($_POST["minbid"]);
        }

        //Validate car license
        if(empty(trim($_POST["license"]))) {
            $license_err = "Please choose a car. ";
        } else {
            $license = trim($_POST["license"]);
        }

		// Check for input errors before inserting in database
        
	    if(empty($origin_err) && empty($dest_err) && empty($pickuptime_err) && empty($minbid_err) && empty($license_err)) {
	        $sql_update_ride = "INSERT INTO ride(origin, dest, pickuptime, minbid, status, carid, advertiserid) 
	                        VALUES ($1, $2, to_timestamp($3, 'mm/dd/yyyy hh:mi AM'), $4, $5,
                            (SELECT carid FROM car WHERE license = $6), $7)";
	        $prepare_update_ride = pg_prepare($db, "", $sql_update_ride);

            if ($prepare_update_ride) {
                $execute_insert_ride = pg_execute($db, "", array($origin, $dest, $pickuptime, $minbid, 'open', $license, $_SESSION['userid']));
                if ($execute_insert_ride) {
                    echo "Update successful!";
                } else {
                    echo "Update failed ".pg_last_error();
                }
            } else {
                echo "Something went wrong!";
            }
	    }
        
	}
}
?>

<!DOCTYPE html>
<html lang = en>
<head>
	<meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/2.14.1/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script type="text/javascript">
      $(function() {
        $('#datetimepicker1').datetimepicker({
        });
      });
    </script>
</head>

<body>
	<div class = "wrapper">
		<h2>Create an Advertisement Today!</h2>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($origin_err)) ? 'has-error' : ''; ?>">
                <label>I am travelling from</label>
                <input type="text" name="origin" class="form-control" value="<?php echo $origin; ?>">
                <span class="help-block"><?php echo $origin_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($dest_err)) ? 'has-error' : ''; ?>">
                <label>To</label>
                <input type="text" name="dest" class="form-control" value="<?php echo $dest; ?>">
                <span class="help-block"><?php echo $dest_err; ?></span>
            </div>

           <div class="form-group <?php echo (!empty($pickuptime_err)) ? 'has-error' : ''; ?>">
              <label class="control-label">Pick up date and time</label>
              <div class='input-group date' id='datetimepicker1'>
                 <input data-format="dd/MM/yyyy hh:mm:ss" type='text' name="pickuptime" class="form-control" value="<?php echo $pickuptime; ?>" >

                 <span class="input-group-addon">
                 <span class="glyphicon glyphicon-calendar"></span>
                 </span>
              </div>
              <span class="help-block"><?php echo $pickuptime_err; ?></span>
           </div>

            <div class="form-group <?php echo (!empty($minbid_err)) ? 'has-error' : ''; ?>">
                <label>I would require a minimum bid of</label>
                <input type="number" name="minbid" value="<?php echo $minbid; ?>" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control" />
                <span class="help-block"><?php echo $minbid_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($license_err)) ? 'has-error' : ''; ?>">
                <label>The car I would like to use is</label>
                    <select name="license" class="form-control" value="<?php echo $license; ?>">
                        <option selected="Select a car to use" disabled="disabled">Select your car</option> 
                        <?php $get_user_cars = "SELECT license from car NATURAL JOIN car_ownedby
                                WHERE ownerid = $1";
                            $prepare_user = pg_prepare($db, "", $get_user_cars);
                            if ($prepare_user) {
                                $execute_user_cars = pg_execute($db, "", array($_SESSION['userid']));
                            }
                        ?>
                        <?php while ($rows = pg_fetch_assoc($execute_user_cars)) {
                                $value= $rows['license']; ?>
                         <option value="<?= $value?>"><?= $value?></option>
                        <?php } ?>
                    </select>
                    <span class="help-block"><?php echo $license_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>

            <p>Do not have a car registered? Register one! <a href="car.php">Register a car here</a>.</p>
		</form>
        <p><a href="adminwelcome.php" class="btn btn-warning">Return to Homepage</a></p>
        <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
	</div>
</body>
</html>