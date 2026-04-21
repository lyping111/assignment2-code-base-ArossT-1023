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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "login") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $message = "Please enter both username and password.";
        $messageType = "error";
    } else {
        $stmt = $connection->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $storedPassword);
            $stmt->fetch();

            if (password_verify($password, $storedPassword)) {
                $_SESSION["user_id"] = $userId;
                $_SESSION["username"] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Invalid username or password.";
                $messageType = "error";
            }
        } else {
            $message = "Invalid username or password.";
            $messageType = "error";
        }

        $stmt->close();
    }
}

$connection->close();
?>
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($message !== ""): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="action" value="login">Login</button>
        </form>
        <div class="link">
            <p>Need an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
