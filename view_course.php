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

// Check if the currently logged-in user is registered for this course
$is_registered = false;
if (isset($_SESSION["user_id"])) {
    $check_stmt = $conn->prepare("SELECT 1 FROM registrations WHERE course_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $course_id, $_SESSION["user_id"]);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $is_registered = true;
    }
}

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
                <img src="assets/<?= htmlspecialchars($row["img"]) ?>" width="1000px" alt="Course Image">
                <h1><?= htmlspecialchars($row["title"]) ?></h1>
                <h3>Room: <?= htmlspecialchars($row["room"]) ?></h3>
                <p>Date: <?= htmlspecialchars($row["course_date"]) ?></p>
                <p><?= htmlspecialchars($row["description"]) ?></p>

                <?php if (isset($_SESSION["message"])): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($_SESSION["message"]) ?></div>
                    <?php unset($_SESSION["message"]); // Clear the message so it doesn't show on next refresh ?>
                <?php endif; ?>

                <?php if (isset($_SESSION["user_id"]) && !$is_registered): ?>
                    <form action="db/register_user_to_course.php" method="POST" class="view-form">
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
                        <button type="submit" class="btn btn-success">Register</button>
                    </form>
                <?php elseif (!isset($_SESSION["user_id"])): ?>
                    <p>
                        <a href="login.php">Log in</a> to register for this course.
                    </p>
                <?php endif; ?>
            
                <hr>
                <h2>Registered users</h2>
                <ul>
                    <?php while ($registered_user = $result->fetch_assoc()): ?>
                        <li><?= htmlspecialchars($registered_user["username"]) ?></li>
                    <?php endwhile; ?>
                </ul>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                    <a href="db/delete_course.php?course_id=<?= htmlspecialchars($course_id) ?>" class="btn btn-danger">
                        Delete course
                    </a>
                <?php endif; ?>

                <?php if ($is_registered): ?>
                    <a href="db/unregister_from_course.php?course_id=<?= htmlspecialchars($course_id) ?>" class="btn btn-danger">Unregister</a>
                <?php endif; ?>

                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
		    <a href="edit_course.php?course_id=<?= htmlspecialchars($course_id) ?>" class="btn btn-primary">Edit course</a>
		<?php endif; ?>
            </div>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>
</html>
