<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "school_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}   
$conn->set_charset("utf8mb4");
?>
