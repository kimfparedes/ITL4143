<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    echo '<script>
            setTimeout(function() {
                let popup = document.createElement("div");
                popup.style.position = "fixed";
                popup.style.top = "50%";
                popup.style.left = "50%";
                popup.style.transform = "translate(-50%, -50%)";
                popup.style.backgroundImage = "url(\'login.jpg\')";
                popup.style.backgroundSize = "cover";
                popup.style.padding = "20px";
                popup.style.borderRadius = "20px";
                popup.style.zIndex = "1000";
                popup.innerHTML = `
                    <p style="color: white;">You\'re in the wrong place, traveler.</p>
                    <button style="background: transparent; border: none; color: white; font-weight: bold; cursor: pointer;" onclick="closePopup()">X</button>
                `;
                document.body.appendChild(popup);
            }, 500);

            function closePopup() {
                document.body.innerHTML = "";
                window.location.href = "Login";
            }
          </script>';
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; 
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

        .homepage-container {
            text-align: center;
            font-size: 108px; 
            font-family: 'Great Vibes', cursive;
            color: white; 
            z-index: 1; 
        }

        .logout-btn {
            margin-top: 50px;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background-color: #f0e68c;
            font-family: 'Great Vibes', cursive;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5), -2px -2px 10px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="background-video">
        <source src="home.mp4" type="video/mp4">
    </video>

    <div class="homepage-container">
        Home Page
        <form action="logout.php" method="post">
            <button class="logout-btn" type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
