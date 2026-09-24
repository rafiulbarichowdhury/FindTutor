<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "findtutor";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("DB connection failed: " . $conn->connect_error); }
?>
