<?php
session_start();
require_once "db/db.php";

// Get course info
$stmt = $conn->prepare("SELECT course_id, title, room, date FROM courses");
$stmt->execute();
$result = $stmt->get_result();
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
                <h1>Home page</h1>
                <?php if ($_SESSION["role"] === "admin"): ?>
                    <h2>Welcome admin</h2>
                    <a href="register.php">Register new user</a>
                <?php else: ?>
                    <h1>Welcome user</h1>
                <?php endif; ?>

                <div class="courses">
                    <h3>Courses</h3>
                    <table>
                        <tr>
                            <th>Title</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>View Details</th>
                        </tr>

                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["title"]) ?></td>
                            <td><?= htmlspecialchars($row["room"]) ?></td>
                            <td><?= htmlspecialchars($row["date"]) ?></td>
			    <td><a href="view_course.php?course_id=<?= htmlspecialchars($row["course_id"]) ?>">View Course</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>

                    <?php if (isset($_SESSION["role"])): ?>
                        <a href="create_course.php">Create a course</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <?php include "includes/footer.php" ?>
    </div>
</body>
</html>
