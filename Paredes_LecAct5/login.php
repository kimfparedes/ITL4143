<?php
include('config.php');
session_start();

// ANTI BRUTE FORCE MEASURE
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

$showPopup = false;

if ($_SESSION['attempts'] >= 3) {
    // SET TIMER
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = time();
    }

    $time_elapsed = time() - $_SESSION['login_time'];
    if ($time_elapsed < 300) {
        $wait_time = 300 - $time_elapsed;
        $showPopup = true;
    } else {
        // RESET TIMER
        $_SESSION['attempts'] = 0;
        unset($_SESSION['login_time']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ANTI SQL-INJECTION MEASURE
    $_POST['username'] = htmlspecialchars($_POST['username']);
    $_POST['password'] = htmlspecialchars($_POST['password']);
    
    $username = $_POST['username'];
    $password = md5($_POST['password']); // MD5 ENCRYPTION

    // ANTI SQL-INJECTION MEASURE
    $query = $conn->prepare("SELECT id FROM Paredes_LecAct5 WHERE username=? AND password=?");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $_SESSION['login_user'] = $username;
        $_SESSION['attempts'] = 0;
        header("location: Home");
        exit(); // 
    } else {
        $error = "Invalid Username or Password";
        $_SESSION['attempts']++;
        if ($_SESSION['attempts'] >= 3) {
            $_SESSION['login_time'] = time();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" href="favicon.ico">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Great Vibes', sans-serif;
            cursor: url('quill.png'), auto; 
        }

        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1; 
        }

        .login-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-image: url('login.jpg');
            background-size: cover;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(255, 255, 0, 0.3); 
            width: 300px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #000;
            font-family: 'Great Vibes', cursive; 
        }

        .login-container label {
            display: block;
            margin-bottom: 5px;
            font-size: 25px;
            color: #000;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
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

        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.2); 
        }

        .login-btn,
        .register-btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background-color: #f0e68c;
            font-weight: bold;
            font-size: 20px;
            font-family: 'Great Vibes', cursive; 
            cursor: pointer;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5), -2px -2px 10px rgba(255, 255, 255, 0.5);
        }

        .login-btn:hover,
        .register-btn:hover {
            background-color: #ffd700;
        }

        #error-message {
            color: red;
        }

        /* POPUP */
        .popup {
            display: <?php echo $showPopup ? 'flex' : 'none'; ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .popup-content h2 {
            margin: 0;
            color: black;
        }
    </style>
</head>
<body>
    <video id="background-video" autoplay muted loop>
        <source src="background.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="login-container">
        <h2>Login</h2>
        <form action="Login" method="post">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button class="login-btn" type="submit">Login</button>
        </form>
        <div id="error-message">
            <?php if (isset($error)): ?>
                <p><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
        <br>
        <a href="Register">
            <button class="register-btn">Create an Account</button>
        </a>
    </div>

    <!-- TOO MANY LOGIN ATTEMPTS POPUP -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <h2>You have failed too many times, my friend. Go and rest for <?php echo isset($wait_time) ? $wait_time : 0; ?> seconds.</h2>
            <button onclick="document.getElementById('popup').style.display='none'">Close</button>
        </div>
    </div>
</body>
</html>
