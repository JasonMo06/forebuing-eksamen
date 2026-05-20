<?php
$servername = "localhost";
$username = "root";
$password = "root";
$db = "forebuing_eksamen";


// Connect to DB
$conn = new mysqli($servername, $username, $password, $db);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($conn->connect_error)
{
    die("Connection Failed: " . $conn->connect_error);
}
?>
