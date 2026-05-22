<?php
session_start();
require_once "db.php"; // Keeps your existing database path since it's inside the db/ folder

// Changed to POST for state-changing operations
if (isset($_SESSION["user_id"]) && $_SERVER["REQUEST_METHOD"] === "POST")
{
    if (!isset($_POST["course_id"])) {
        die("Missing course_id");
    }

    $user_id = $_SESSION["user_id"];
    $course_id = (int) $_POST["course_id"];

    // Prevent duplicate registrations
    $check = $conn->prepare("SELECT * FROM registrations WHERE user_id = ? AND course_id = ?");
    $check->bind_param("ii", $user_id, $course_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0)
    {
        $register = $conn->prepare("INSERT INTO registrations (user_id, course_id) VALUES (?, ?)");
        $register->bind_param("ii", $user_id, $course_id);

        if ($register->execute())
        {
            $_SESSION["message"] = "You are now registered for the course.";
        }
        else
        {
            $_SESSION["message"] = "Something went wrong.";
        }
    }
    else
    {
        $_SESSION["message"] = "You are already registered.";
    }

    // Redirect cleanly back to the course details view page
    header("Location: ../view_course.php?course_id=" . $course_id);
    exit();
} else {
    // If someone tries to access this script file directly without posting
    header("Location: ../index.php");
    exit();
}
?>
