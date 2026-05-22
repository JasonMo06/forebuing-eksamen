<?php
session_start();
require_once "db/db.php";

// Restrict to admins only
if (!isset($_SESSION["role"]))
{
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $title = trim($_POST["title"]);
    $room = trim($_POST["room"]);
    $description = trim($_POST["description"]);
    $img = trim($_POST["img"]);
    $course_date = trim($_POST["course_date"]);

    // Default fallback image if the input field was left blank
    if (empty($img)) {
        $img = "default.jpg";
    }

    // FIX 1: Changed 'Img' to lowercase 'img' to match your database column perfectly
    $stmt = $conn->prepare("INSERT INTO courses (title, room, description, img, course_date) VALUES (?, ?, ?, ?, ?)");
    
    // FIX 2: Updated "ssss" to "sssss" because there are 5 values being bound
    $stmt->bind_param("sssss", $title, $room, $description, $img, $course_date);
    
    if ($stmt->execute()) {
        $_SESSION["message"] = "Course created successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Database Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home page</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <?php include "includes/header.php" ?>

        <main>
            <div class="inner-main">
                <h1>Create Course</h1>
                <form method="POST">
                    <label for="title">Title:</label>
                    <input type="text" name="title"><br><br>

                    <label for="room">Room:</label>
                    <select name="room">
                        <option value="Room 1">Room 1</option>
                        <option value="Room 2">Room 2</option>
                        <option value="Room 3">Room 3</option>
                        <option value="Room 4">Room 4</option>
                        <option value="Room 5">Room 5</option>
                    </select><br><br>

		    <label for="description">Description:</label>
		    <input type="text" name="description"><br><br>

		    <label for="img">Image path:</label>
                    <input type="text" name="img"><br><br>

                    <label for="course_date">Date:</label>
                    <input type="date" name="course_date"><br><br>

		    <button type="submit" class="btn btn-success">Create Course</button>
                </form>
            </div>
        </main>

        <?php include "includes/footer.php" ?>
    </div>
</body>
</html>
