<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]))
{
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET["course_id"]))
{
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$course_id = (int)$_GET["course_id"];

$stmt = $conn->prepare("DELETE FROM registrations WHERE user_id = ? AND course_id = ?");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();

$_SESSION["message"] = "You have successfully unregistered from this course.";

header("Location: ../view_course.php?course_id=" . $course_id);
exit();
?>
