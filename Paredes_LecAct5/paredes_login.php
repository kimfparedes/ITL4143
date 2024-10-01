<?php
session_start();
include 'db_connection.php'; // Assume this file contains your DB connection logic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // FETCH USER FROM DB
    $stmt = $conn->prepare("SELECT password FROM Paredes_LecAct5 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // PASSWORD VERIFY
        if (password_verify($password, $hashed_password)) {
            echo '<script>alert("Login Successful.");</script>';
        } else {
            echo '<script>alert("Invalid login credentials.");</script>';
        }
    } else {
        echo '<script>alert("Invalid login credentials.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <p><a href="Register">Create an Account</a></p>
</body>
</html>
