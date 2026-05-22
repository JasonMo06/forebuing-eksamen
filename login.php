<?php
session_start();
require_once "db/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1)
    {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"]))
        {
            // Store user session
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit;
        }
        else
        {
            $error = "Invalid password";
        }
    }
    else
    {
        $error = "Incorrect username or password";
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
        <title>Login</title>
	<link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div class="container">
            <div class="register-box">
                <h2>Login</h2>

                <div class="error">
                    <?php if (isset($error)) { echo $error; } ?>
                </div>

                <form method="post">
                    <label for="username">Username</label><br>
                    <input type="text" name="username" placeholder="Enter your username" required><br><br>

                    <label for="password">Password</label><br>
                    <input type="password" name="password" placeholder="Enter your password" required><br>

                    <button type="submit" class="btn btn-success">Login</button><br><br>
		    
		    <a href="customer_register.php">Create Account</a><br>
		    <a href="index.php">Back to Home Page</a>
                </form>
            </div>
        </div>
    </body>
</html>
