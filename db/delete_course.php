<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET["course_id"])) {
    die("Missing course_id");
}

$course_id = (int) $_GET["course_id"];

$stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $course_id);

if (!$stmt->execute()) {
    die("Delete failed: " . $stmt->error);
}

header("Location: ../index.php");
exit();
?>
