<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database_name = "e_commerce";
$db_port = 3307; 

$conn = mysqli_connect($servername, $db_username, $db_password, $database_name, $db_port);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
} 


$GLOBALS['conn'] = $conn;