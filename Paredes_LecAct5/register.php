<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ERROR TRAPPING
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        echo '<script>alert("All fields must be filled.");</script>';
    } else {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $confirm_password = htmlspecialchars($_POST['confirm_password']);

        // VERIFY PASSWORD
        if ($password !== $confirm_password) {
            echo '<script>alert("Passwords don\'t match.");</script>';
        } else {
            // VERIFY CAPTCHA
            $recaptcha_secret = '6Lc87lQqAAAAAP3LeaSZRhX0sco279o0Bq2pOXKm';
            $recaptcha_response = $_POST['g-recaptcha-response'];

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
            $response_keys = json_decode($response, true);

            if (!$response_keys["success"]) {
                echo '<script>alert("Invalid captcha.");</script>';
            } else {
                // MD5 ENCRYPTION
                $hashed_password = md5($password);

                // ALREADY EXISTS ERROR TRAPPING
                $check_query = $conn->prepare("SELECT * FROM Paredes_LecAct5 WHERE username = ?");
                $check_query->bind_param("s", $username);
                $check_query->execute();
                $result = $check_query->get_result();

                if ($result->num_rows > 0) {
                    echo '<script>alert("Username already exists.");</script>';
                } else {
                    // INSERT NEW USER INTO DB
                    $query = $conn->prepare("INSERT INTO Paredes_LecAct5 (username, password) VALUES (?, ?)");
                    $query->bind_param("ss", $username, $hashed_password);

                    if ($query->execute()) {
                        echo '<script>alert("Registration successful."); window.location.href = "Login";</script>';
                    } else {
                        echo '<script>alert("Registration failed. Please try again.");</script>';
                    }
                }
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
    <link rel="icon" href="favicon.ico">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Great Vibes', sans-serif;
            cursor: url('quill.png'), auto; 
            background-image: url('register.jpg'); 
            background-size: cover; 
            background-attachment: fixed;
        }

        .register-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(255, 255, 0, 0.3);
            width: 300px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #000;
            font-family: 'Great Vibes', cursive; 
        }

        .register-container label {
            display: block;
            margin-bottom: 5px;
            font-size: 25px;
            color: #000;
        }

        .register-container input[type="text"],
        .register-container input[type="password"] {
            width: 90%;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.5);
            margin-bottom: 15px;
            outline: none;
            font-size: 16px;
            text-align: center;
            transition: background 0.3s; 
        }

        .register-container input[type="text"]:focus,
        .register-container input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.2); 
        }

        .register-btn,
        .back-btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background-color: #f0e68c;
            font-weight: bold;
            font-size: 20px;
            font-family: 'Great Vibes', cursive; 
            cursor: pointer;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5), -2px -2px 10px rgba(255, 255, 255, 0.5);
            margin-top: 10px;
        }

        .register-btn:hover,
        .back-btn:hover {
            background-color: #ffd700;
        }

        #error-message {
            color: red;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form action="Register" method="post">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
            <div class="g-recaptcha" data-sitekey="6Lc87lQqAAAAAPTtvZwFAmcnl_4w7s9Wsmzp5A7i"></div>
            <button class="register-btn" type="submit">Register</button>
        </form>
        <a href="Login">
            <button class="back-btn">Back to Login</button>
        </a>
    </div>
</body>
</html>

