<?php 
/* Attempt to connect to PostgresSQL database */
$db   = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=password");
 
// Check connection
if($db === false){
    die("ERROR: Could not connect. " . pg_errormessage());
}
?>