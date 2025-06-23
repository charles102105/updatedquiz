<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";

$error = "";
$conn = null;


try {
    $conn = new mysqli($host, $user, $pass, $db);
    

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    $error = "Database connection failed. Please try again later.";
}

$from_logout = isset($_GET['logout']) && $_GET['logout'] == '1';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $conn) {
    $username = trim(htmlspecialchars($_POST['user']));
    
    if (empty($username)) {
        $error = "Please enter a username.";
    } else {
        try {
            
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true; 
                $stmt->close();
                $conn->close();
                
        
                header("Location: q1.php");
                exit();
            } else {
        
                $stmt->close();
                
                $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, '')");
                if (!$stmt_insert) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt_insert->bind_param("s", $username);
                
                if ($stmt_insert->execute()) {
                    $new_user_id = $conn->insert_id;
                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['logged_in'] = true; 
                    $stmt_insert->close();
                    $conn->close();
                    
                   
                    header("Location: q1.php");
                    exit();
                } else {
                    throw new Exception("Error creating account: " . $stmt_insert->error);
                }
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}


$force_login = isset($_GET['force']) && $_GET['force'] == '1';


if ($conn && 
    !isset($_POST['submit']) && 
    isset($_SESSION['user_id']) && 
    !empty($_SESSION['user_id']) && 
    !$from_logout &&
    !$force_login &&
    isset($_SESSION['logged_in']) && 
    $_SESSION['logged_in'] === true) {
    
 
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $stmt->close();
                $conn->close();
                header("Location: q1.php");
                exit();
            } else {
                
                session_destroy();
                session_start();
            }
            $stmt->close();
        }
    } catch (Exception $e) {
      
    }
}


if ($conn) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(249, 224, 250);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 350px;
            max-width: 90%;
            text-align: center;
            border: 2px solid #7d4caf;
        }
        .form-container h2 {
            color: rgb(176, 25, 210);
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .form-container label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            text-align: left;
            font-weight: bold;
        }
        .form-container input[type="text"] {
            width: calc(100% - 24px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            border-left: 5px solid #7d4caf;
        }
        .form-container input[type="text"]:focus {
            border-color: rgb(255, 168, 232);
            outline: none;
            box-shadow: 0 0 8px rgba(63, 169, 245, 0.5);
        }
        .form-container input[type="submit"] {
            background-color: #7d4caf;
            color: white;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .form-container input[type="submit"]:hover {
            background-color: rgb(226, 177, 255);
        }
        .error {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffe6e6;
            border-radius: 5px;
            border-left: 4px solid #ff0000;
        }
        .info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e3f2fd;
            border-radius: 5px;
            font-size: 0.9em;
            color: #1976d2;
        }
        .success {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #e8f5e8;
            border-radius: 5px;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }
        .debug {
            margin-top: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 0.8em;
            color: #666;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        
        <?php if ($from_logout): ?>
            <div class="success">You have been successfully logged out.</div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <label for="username">Enter your username</label>
            <input type="text" id="username" name="user" placeholder="Your Username" required>
            <input type="submit" value="Login Now" name="submit" id="submit">
        </form>
        
        <?php if (isset($_SESSION['user_id']) && !$from_logout): ?>
            <div class="debug">
                <strong>Debug Info:</strong><br>
                Session User ID: <?php echo $_SESSION['user_id']; ?><br>
                Session Username: <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Not set'; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>