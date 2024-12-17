<?php
$host = 'localhost';
$username = 'root'; //'benson.vorsah';
$password = ''; //'150803';
$dbname = 'abahubdb';

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    error_log("Database Connection Error: " .mysqli_connect_error());
    die("Sorry, there was a problem connecting to the database.");
}
?>
