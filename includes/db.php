<?php
$host = "127.0.0.1";
$user = "root"; 
$pass = ""; 
$dbname = "admin_panel";

$conn = mysqli_connect($host, $user, $pass, $dbname, 3307);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
