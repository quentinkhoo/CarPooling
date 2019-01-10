
<?php
// Initialize the session
require_once '../config.php';
session_start();

if(!isset($_SESSION['userid']) || empty($_SESSION['userid']) 
  || !isset($_SESSION['isadmin']) || empty($_SESSION['isadmin'])) {
  header("location: ../login.php");
  exit;
}
 
 $result = pg_query($db, "SELECT * FROM Person WHERE userid = ".$_SESSION['userid']);
 if (!$result) {
  echo "An error occured. \n";
 }

$fullname = $email = $phone = $username = $password = $confirmpassword = "";
$fullname_err = $email_err= $phone_err = $username_err = $password_err = $confirmpassword_err = "";

//fetching user's data
$row = pg_fetch_assoc($result);
//allocating user data in array
$username = $row[username];
$email = $row[email];
$fullname = $row[fullname];
$phone = $row[phone];

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username. ";
    }else{
        $username = trim($_POST["username"]);
    }

    //validate fullname
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Please enter your fullname. ";
    }else{
        $fullname = trim($_POST["fullname"]);
    }

        //validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email. ";
    }else{
        $email = trim($_POST["email"]);
    }

        //validate phone
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter your phone. ";
    }else{
        $phone = trim($_POST["phone"]);
    }

    //update profile
    if(empty($fullname_err) && empty($email_err) && empty($phone_err)){
        $sql_update = "UPDATE person SET fullname = $1, email = $2, phone = $3, username = $4 WHERE userid = $5 ";
        $prepare_update = pg_prepare($db, "", $sql_update);
        if ($prepare_update) {
            $execute_update = pg_execute($db, "", array($fullname, $email, $phone, $username, $_SESSION['userid']));
        }
         
    }

    pg_close($db);
    

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="css/style.css" />
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }

    </style>
</head>

<body id = "page-op">

  <nav class="navbar navbar-default">
  <div class="container-fluid">
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Car Pooling <span class="sr-only">(current)</span></a></li>
        <li><a href="adminupdatebid.php">My Bids</a></li>
        <li><a href="adminupdateads.php">My Advertisements</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="viewallusers.php">View All Users</a></li>
            <li><a href="viewallads.php">View All Advertisements</a></li>
            <li><a href="viewallbids.php">View All Bids</a></li>
            <li><a href="adminprofile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
  </div>

  </div>
</nav>


    <div class="container">
    <div class="row profile">
    <div class="col-md-3">
      <div class="profile-sidebar">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
          <img src="./img/avatarless.jpg" class="img-responsive" alt="">
        </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
          <div class="profile-usertitle-name">
          </div>
          <div class="profile-usertitle-job">
            Admin
          </div>
        </div>
        <!-- END SIDEBAR USER TITLE -->

        <!-- END SIDEBAR BUTTONS -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
          <ul class="nav">
            <li class="active">
              <a href="adminprofile.php">
              <i class="glyphicon glyphicon-home"></i>
              Profile </a>
              </li>
             <li class="active">
              <a href="adminviewcars.php">
              <i class="glyphicon glyphicon-road"></i>
              View my Registered Cars </a>
            </li>
            </li>
             <li class="active">
              <a href="admincar.php">
              <i class="glyphicon glyphicon-road"></i>
              Register a Car </a>
            </li>
           
          </ul>
        </div>
        <!-- END MENU -->
      </div>
    </div>
    <div class="col-md-9">
       <div class="profile-content" id = "profile">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>

             <div class="form-group <?php echo (!empty($fullname_err)) ? 'has-error' : ''; ?>">
                <label>Full Name</label>
                <input type="text" name="fullname" class="form-control" value="<?php echo $fullname; ?>">
                <span class="help-block"><?php echo $fullname_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                <span class="help-block"><?php echo $phone_err; ?></span>
            </div>

            <div class = "row">
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update Profile">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>

            </div>
          </form>
        </div>

      </div>
  </div>
</div>

</body>
</html>