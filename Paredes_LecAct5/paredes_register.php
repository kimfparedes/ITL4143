<?php
session_start();
include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // INPUT FIELDS ERROR TRAPPING
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo '<script>alert("All fields must be filled.");</script>';
    } elseif ($password !== $confirm_password) {
        echo '<script>alert("Passwords don\'t match.");</script>';
    } elseif (empty($recaptcha_response)) {
        echo '<script>alert("Invalid captcha.");</script>';
    } else {
        // VERIFY RECAPTCHA
        $secret_key = '6Lc87lQqAAAAAP3LeaSZRhX0sco279o0Bq2pOXKm'; // SECRET KEY
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
        $response_keys = json_decode($response, true);
        
        if (intval($response_keys["success"]) !== 1) {
            echo '<script>alert("Invalid captcha.");</script>';
        } else {
            // HASH PASSWORD
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // STORE USER IN DB
            $stmt = $conn->prepare("INSERT INTO Paredes_LecAct5 (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                echo '<script>alert("Registration successful."); window.location.href="Login";</script>';
            } else {
                echo '<script>alert("Error: ' . $stmt->error . '");</script>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <h2>Register</h2>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <!-- SITE KEY -->
        <div class="g-recaptcha" data-sitekey="6Lc87lQqAAAAAPTtvZwFAmcnl_4w7s9Wsmzp5A7i"></div>
        <br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
