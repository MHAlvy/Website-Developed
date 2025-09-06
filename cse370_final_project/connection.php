<?php

$servername = "localhost";   
$username = "root";         
$password = "";             
$dbname = "project";       

$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8");
?>
