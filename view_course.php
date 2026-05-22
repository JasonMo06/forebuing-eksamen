<?php
session_start();
require_once "db/db.php";

// Fetch and show course
$course_id = $_GET["course_id"];

$stmt = $conn->prepare("SELECT title, room, description, date FROM courses WHERE course_id = ?");
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

// Register user to course if logged in
if (isset($_SESSION["user_id"]) && $_SERVER["REQUEST_METHOD"] === "POST")
{
    $user_id = $_SESSION["user_id"];

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
            $message = "You are now registered for the course.";
        }
        else
        {
            $message = "Something went wrong.";
        }
    }
    else
    {
        $message = "You are already registered.";
    }
}
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
                <img src="assets/ikt-kurs-image" alt="Course image">
                <h1><?= htmlspecialchars($row["title"]) ?></h1>
                <h3>Room: <?= htmlspecialchars($row["room"]) ?></h3>
                <p>Date: <?= htmlspecialchars($row["date"]) ?></p>
                <p><?= htmlspecialchars($row["description"]) ?></p>

                <?php if (isset($message)): ?>
                    <p><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <form method="POST">
                        <button type="submit" name="registration">Register</button>
                    </form>
                <?php else: ?>
                    <p>
                        <a href="login.php">Log in</a> to register for this course.
                    </p>
                <?php endif; ?>
	    
		<hr>
		<h2>Registered users</h2>
		<?php while ($row = $result->fetch_assoc()): ?>
		    <ul>
			<li><?= htmlspecialchars($row["username"]) ?></li>
		    </ul>
		<?php endwhile; ?>

		<?php if ($_SESSION["role"] === "admin"): ?>
		    <a href="delete_course.php?course_id=<?= htmlspecialchars($course_id) ?>">Delete course</a>
		<?php endif; ?>
            </div>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>
</html>
