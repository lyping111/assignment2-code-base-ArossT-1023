<?php
session_start();
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "user_system";

$connection = new mysqli($db_host, $db_user, $db_pass);
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

$connection->query("CREATE DATABASE IF NOT EXISTS $db_name");
$connection->select_db($db_name);
$connection->query(
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
);

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "register") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $message = "Please enter both username and password.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $messageType = "error";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            // Auto-login after registration
            $_SESSION["user_id"] = $connection->insert_id;
            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            if ($connection->errno === 1062) {
                $message = "Username already exists. Please choose another username.";
                $messageType = "error";
            } else {
                $message = "Registration failed: " . $connection->error;
                $messageType = "error";
            }
        }
        $stmt->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Registration</h1>
        <?php if ($message !== ""): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required minlength="6">

            <button type="submit" name="action" value="register">Register</button>
        </form>
        <div class="link">
            <p>Already registered? <a href="login.php">Login here</a>.</p>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
