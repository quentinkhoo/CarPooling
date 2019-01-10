<?php
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])){
  header("location: login.php");
  exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
        <li><a href="updatebid.php">My Bids</a></li>
        <li><a href="updateads.php">My Advertisements</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Statistics <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="origindest.php">Origin/Destination Count</a></li>
            <li><a href="userstats.php">User Count</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
  </div>

  </div>
</nav>


 <div class="jumbotron">
        <div class="container-fluid">         
        <!-- <div><img src="./img/download.jpg" alt="John" style="width:100%"></div>    -->   
            <h2>Hi. Welcome to our site.</h2>

            <br><br>
         <div class ="wrapper">
            <ul class = "list-inline">
                <li c>
                <a href="advertisements.php" class="btn btn-info" role="button" aria-pressed="true">Make A Bidding</a>
                </li>


                 <li>
                <a href="advertise.php" class="btn btn-info" role="button" aria-pressed="true">Advertise a Ride</a>
                </li>

            </ul>
            </div> 
        </div>
</html>