<?php
session_start();
require_once "db/db.php";

if (!isset($_GET["course_id"])) {
    die("Missing course_id");
}

$course_id = (int)$_GET["course_id"];

// Fetch and show course
$stmt = $conn->prepare("SELECT title, room, description, img, course_date FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Fetch and show all course registrations
$stmt = $conn->prepare("
    SELECT users.username
    FROM users
    INNER JOIN registrations ON users.user_id = registrations.user_id
    WHERE course_id = ?
");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Course page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include "includes/header.php"; ?>

        <main>
            <div class="inner-main">
                <img src="assets/<?= htmlspecialchars($row["img"]) ?>" width="1000px">
                <h1><?= htmlspecialchars($row["title"]) ?></h1>
                <h3>Room: <?= htmlspecialchars($row["room"]) ?></h3>
                <p>Date: <?= htmlspecialchars($row["course_date"]) ?></p>
                <p><?= htmlspecialchars($row["description"]) ?></p>

                <?php if (isset($_SESSION["message"])): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($_SESSION["message"]) ?></div>
                    <?php unset($_SESSION["message"]); // Clear the message so it doesn't show on next refresh ?>
                <?php endif; ?>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <form action="db/register_user_to_course.php" method="POST" class="view-form">
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
                        <button type="submit" class="btn btn-success">Register</button>
                    </form>
                <?php else: ?>
                    <p>
                        <a href="login.php">Log in</a> to register for this course.
                    </p>
                <?php endif; ?>
            
                <hr>
                <h2>Registered users</h2>
                <?php while ($registered_user = $result->fetch_assoc()): ?>
                    <ul>
                        <li><?= htmlspecialchars($registered_user["username"]) ?></li>
                    </ul>
                <?php endwhile; ?>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <a href="db/delete_course.php?course_id=<?= htmlspecialchars($course_id) ?>" class="btn btn-danger">Delete course</a>
                <?php endif; ?>

		<!-- Remove yourself from course -->
		<!-- TODO: if (hide unregister button when you are not registered) -->

		<a href="db/unregister_from_course.php?course_id=<?= htmlspecialchars($course_id) ?>" class="btn btn-danger">Unregister</a>
            </div>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>
</html>
