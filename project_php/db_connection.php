<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hb_database1";

$conn = new mysqli($servername, $username, $password, $dbname = "hb_database1");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>