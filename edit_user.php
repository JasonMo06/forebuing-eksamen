<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION["user_id"]))
{
    header("Location: login.php");
    exit();
}

// Fetch user info and show on the page
$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST["username"];

    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();

    header("Location: index.php");
    exit();
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
		<h1>Edit profile</h1>

		<form method="POST">
		    <label for="username">Username:</label>
		    <input type="text" name="username" value="<?= htmlspecialchars($row["username"]) ?>"><br><br>

		    <button type="submit" class="btn btn-primary">Update profile</button>
		</form>
	    </div>
	</main>

	<?php include "includes/footer.php" ?>
    </div>
</body>
</html>
