<?php

session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $username = $conn->real_escape_string($username);
 
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    
    if (password_verify($password, $hashed_password)) {
        $_SESSION['admin_id'] = $username;
        header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title> 
    <link rel="stylesheet" type="text/css" href="css/king.css">
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </form>
</body>
</html>
