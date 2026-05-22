<?php
session_start();
require_once "db/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin")
{
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Get form data
    $role = trim($_POST["role"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $repeat_password = trim($_POST["repeat_password"]);

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if username is already used
    if ($stmt->num_rows > 0)
    {
        $error = "Username is already registered!";
    }
    else
    {
        // Check if password and repeated passwords match 
        if ($password != $repeat_password)
        {
            $error = "Passwords does not match";  
        }
        else
        {
            // Hash password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $role);
            
            if ($stmt->execute())
            {
                echo "Registration successful!";
            }
            else
            {
                echo "Error: " . $conn->error;
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
	<link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div class="container">
            <div class="register-box">
                <h2>Register</h2>

                <div class="error">
                    <?php if (isset($error)) { echo $error; } ?>
                </div>

                <form method="POST">
                    <label>Role:</label><br>
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="tilsett">Tilsett</option>
                        <option value="instructor">Instructor</option>
                    </select><br><br>

                    <label>Username:</label><br>
                    <input type="text" name="username" placeholder="Enter your username" required><br><br>

                    <label>Password:</label><br>
                    <input type="password" name="password" placeholder="Enter your password" required><br><br>

                    <label>Repeat password:</label><br>
                    <input type="password" name="repeat_password" placeholder="Please repeat your password" required><br><br>
        
                    <button type="submit" class="btn btn-success">Register</button><br><br>

                    <a href="index.php">Back to home page</a>
                </form>
            </div>
        </div>
    </body>
</html>
