<?php
//NOT TO BE CONFUSED WITH ADVERTISEMENTS.PHP, THIS FILE IS TO CREATE ADVERTISEMENTS

require_once '../config.php';

session_start();

date_default_timezone_set('Asia/Singapore');
 
//Define all the variables I need to use first
$model = $license = $colour = $seats = "";
$model_err = $license_err = $colour_err = $seats_err = "";

//Redirect to login page if session has not started

if(!isset($_SESSION['userid']) || empty($_SESSION['userid']) 
  || !isset($_SESSION['isadmin']) || empty($_SESSION['isadmin'])) {
    header("location: ../login.php");
  exit;
} else {
  //Processing form data when form is submitted
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Validate origin
    if(empty(trim($_POST["license"]))){
          $license_err = "Please enter a car license. ";
      } else {
          $license = trim($_POST["license"]);
      }

      //Validate destination
      if(empty(trim($_POST["model"]))){
          $model_err = "Please enter your car model. ";
      } else {
          $model = trim($_POST["model"]);
      }

      //Validate input date
      if(empty(trim($_POST["colour"]))){
          $colour_err = "Please enter your car colour. ";
      } else {
          $colour = trim($_POST["colour"]);
      }

        //Validate input mininum bid
        if(empty(trim($_POST["seats"]))) {
            $seats_err = "Please select the number of seats your car has. ";
        } else {
            $seats = trim($_POST["seats"]);
        }

    // Check for input errors before inserting in database
        
      if(empty($seats_err) && empty($colour_err) && empty($model_err) && empty($license_err)) {
          $sql_update_car = "INSERT INTO car(license, model, colour, seats) 
                          VALUES ($1, $2, $3, $4)";
          $prepare_update_car = pg_prepare($db, "", $sql_update_car);

            if ($prepare_update_car) {

                $execute_insert_car = pg_execute($db, "", array($license, $model, $colour, $seats));
                if ($execute_insert_car) {

                      $sql_carid = "SELECT carid FROM car WHERE license = $1";
                      $prepare_carid = pg_prepare($db, "", $sql_carid);
                      if ($prepare_carid) {
                          $execute_get_carid = pg_execute($db, "", array($license));
                          if ($execute_get_carid) {
                              $rows = pg_fetch_assoc($execute_get_carid);
                              $carid = $rows[carid];

                              $sql_update_carowner = "INSERT INTO car_ownedby(ownerid, carid) VALUES ($1, $2)";
                              $prepare_update_carowner = pg_prepare($db, "", $sql_update_carowner);
                              if ($prepare_update_carowner) {
                                $execute_insert_carowner = pg_execute($db, "", array($_SESSION['userid'], $carid));
                                if ($execute_insert_carowner) {
                                  echo "Update successful!";
                                } else {
                                  echo "Update failed";
                                }
                              }
                              
                          }
                      } 
                  
                    
                } else {
                  echo "Car is already registered. Assigning you as owner...";
                  $sql_carid = "SELECT carid FROM car WHERE license = $1";
                  $prepare_carid = pg_prepare($db, "", $sql_carid);
                  if ($prepare_carid) {
                      $execute_get_carid = pg_execute($db, "", array($license));
                      if ($execute_get_carid) {
                          $rows = pg_fetch_assoc($execute_get_carid);
                          $carid = $rows[carid];

                          $sql_update_carowner = "INSERT INTO car_ownedby(ownerid, carid) VALUES ($1, $2)";
                          $prepare_update_carowner = pg_prepare($db, "", $sql_update_carowner);
                          if ($prepare_update_carowner) {
                            $execute_insert_carowner = pg_execute($db, "", array($_SESSION['userid'], $carid));
                            if ($execute_insert_carowner) {
                              echo "Update successful!";
                            } else {
                              echo "Update failed";
                            }
                          }
                          
                      }
                  }
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
    <title>Register A Car</title>
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
    <h2>Register a car today!</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group <?php echo (!empty($license_err)) ? 'has-error' : ''; ?>">
                <label>Car License Plate</label>
                <input type="text" name="license" class="form-control" value="<?php echo $license; ?>">
                <span class="help-block"><?php echo $license_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($model_err)) ? 'has-error' : ''; ?>">
                <label>Car Model</label>
                <input type="text" name="model" class="form-control" value="<?php echo $model; ?>">
                <span class="help-block"><?php echo $model_err; ?></span>
            </div>

           <div class="form-group <?php echo (!empty($colour_err)) ? 'has-error' : ''; ?>">
              <label class="control-label">Car Colour</label>
               <input type="text" name="colour" class="form-control" value="<?php echo $colour; ?>">
                <span class="help-block"><?php echo $colour_err; ?></span>
           </div>

            <div class="form-group <?php echo (!empty($seats_err)) ? 'has-error' : ''; ?>">
                <label>Number of Seats </label>
                <select name="seats" class="form-control" value="<?php echo $seats; ?>">
                    <option value="" disabled selected>Number of Seats (Including Driver)</option>
                    <option value="2-seater">2-seater</option>
                    <option value="5-seater">5-seater</option>
                    <option value="7-seater">7-seater</option>
                </select>
                <span class="help-block"><?php echo $seats_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add Car">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
    </form>
    <p><a href="adminwelcome.php" class="btn btn-warning">Return to Homepage</a></p>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
  </div>
</body>
</html>