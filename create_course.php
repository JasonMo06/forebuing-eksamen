<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION["role"]))
{
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $title = trim($_POST["title"]);
    $room = trim($_POST["room"]);
    $date = trim($_POST["date"]);

    $stmt = $conn->prepare("INSERT INTO courses (title, room, date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $room, $date);
    $stmt->execute();
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
                        <option value="room 1">Room 1</option>
                        <option value="room 2">Room 2</option>
                        <option value="room 3">Room 3</option>
                        <option value="room 4">Room 4</option>
                        <option value="room 5">Room 5</option>
                    </select><br><br>

                    <label for="date">Date:</label>
                    <input type="date" name="date"><br><br>

                    <input type="submit" value="Create Course">
                </form>
            </div>
        </main>

        <?php include "includes/footer.php" ?>
    </div>
</body>
</html>
