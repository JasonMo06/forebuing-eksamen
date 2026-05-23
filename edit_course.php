<?php
session_start();
require_once "db/db.php";

// Editing course should only be allowed by admins
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") 
{
    header("Location: login.php");
    exit();
}

if (!isset($_GET["course_id"])) 
{
    die("Missing course_id");
}

$course_id = (int)$_GET["course_id"];

// Update the values IF the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $title = $_POST["title"]; 
    $room = $_POST["room"]; 
    $description = $_POST["description"]; 
    $img = $_POST["img"]; 
    $course_date = $_POST["course_date"]; 

    // Use UPDATE to modify an existing record
    $stmt = $conn->prepare("UPDATE courses SET title = ?, room = ?, description = ?, img = ?, course_date = ? WHERE course_id = ?");
    $stmt->bind_param("sssssi", $title, $room, $description, $img, $course_date, $course_id);
    $stmt->execute();

    header("Location: view_course.php?course_id=" . $course_id);
    exit();
}

// Fetch current course values
$stmt = $conn->prepare("SELECT title, room, description, img, course_date FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) 
{
    die("Course not found.");
}
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Course</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include "includes/header.php"; ?>  

        <main>
            <div class="inner-main">
                <h1>Edit course</h1>      

                <form method="POST" action="">
                    <label for="title">Title:</label> 
                    <input type="text" name="title" value="<?= htmlspecialchars($row["title"]) ?>"><br><br>

                    <label for="room">Room:</label> 
                    <select name="room">
                        <?php 
                        $rooms = ["Room 1", "Room 2", "Room 3", "Room 4", "Room 5"];
                        foreach ($rooms as $r): 
                            // Check if this room matches the database value
                            $is_selected = ($row["room"] === $r) ? "selected" : "";
                        ?>
                            <option value="<?= htmlspecialchars($r) ?>" <?= $is_selected ?>><?= htmlspecialchars($r) ?></option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <label for="description">Description:</label> 
                    <input type="text" name="description" value="<?= htmlspecialchars($row["description"]) ?>"><br><br>

                    <label for="img">Image path:</label> 
                    <input type="text" name="img" value="<?= htmlspecialchars($row["img"]) ?>"><br><br>

                    <label for="course_date">Date:</label> 
                    <input type="date" name="course_date" value="<?= htmlspecialchars($row["course_date"]) ?>"><br><br>

                    <button type="submit" class="btn btn-primary">Confirm changes</button>
                </form>
            </div>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>
</html>
