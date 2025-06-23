<?php
session_start();


$username_display = '';
if (!empty($_SESSION['username'])) {
    $username_display = htmlspecialchars($_SESSION['username']);
} elseif (!empty($_SESSION['username'])) {
    $username_display = htmlspecialchars($_SESSION['usename']);
}


$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(249, 224, 250);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
         
        .logout-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 350px;
            max-width: 90%;
            text-align: center;
            border: 2px solid #7d4caf;
        }
         
        .logout-container h2 {
            color:rgb(176, 25, 210);
            margin-bottom: 20px;
            font-size: 2.2em;
        }
         
        .logout-container p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 15px;
        }
         
        .logout-container p.username-message {
            font-size: 1.3em;
            font-weight: bold;
            color: #7d4caf;
            margin-bottom: 20px;
        }
         
        .redirect-message {
            font-size: 0.9em;
            color: #888;
            margin-top: 25px;
        }
    </style>
</head>
<body>
<div class="logout-container">
    <h2>Logging Out</h2>
    <?php if (!empty($username_display)): ?>
        <p class="username-message">Thank you, <?php echo $username_display; ?>!</p>
    <?php else: ?>
        <p class="username-message">Thank you!</p>
    <?php endif; ?>
    <p>Your answers were submitted successfully.</p>
    <p>Thank you for taking this quiz!</p>
    <p class="redirect-message">You will be redirected to the login page shortly.</p>
</div>

<script>
    window.onload = function() {
        setTimeout(function() {
            window.location.href = "login.php?logout=1";
        }, 3000);
    };
</script>
</body>
</html>