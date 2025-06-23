<?php
ob_start();

session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] === 'Guest') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $_SESSION['q4'] = $_POST['q4'] ?? null;

    session_write_close();

    header("Location: q5.php");
    exit();
}

$usernamename_display = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question 4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(249, 224, 250);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .quiz-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
            border-top: 5px solid #7d4caf;
        }

        .welcome-message {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .question-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .question-text {
            font-size: 1.4em;
            color: #2c3e50;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .form-check-label {
            cursor: pointer;
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            text-align: left;
            display: flex;
            align-items: center;
            font-size: 1.1em;
            color: #555;
        }

        .form-check-label:hover {
            background-color: #f5f5f5;
            border-color: #7d4caf;
        }

        .form-check-input {
            margin-right: 15px;
            transform: scale(1.4);
            accent-color: #7d4caf;
        }
        .btn-success {
            background-color: #7d4caf !important;
            border-color: #7d4caf !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #6a3e9c !important;
            border-color: #6a3e9c !important;
        }
    </style>
</head>
<body>
<div class="quiz-container">
    <div class="welcome-message">
        <?php echo "Welcome, " . $usernamename_display . "!"; ?>
    </div>

    <form action="q4.php" method="post">
        <img src="images/harddrive.jpg" alt="Hard Drive Image" class="question-image">

        <div class="question-text">
            4. What is the name of this part?
        </div>

        <div class="mb-3">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" value="a" name="q4" required>
                A. CPU
            </label>
        </div>
        <div class="mb-3">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" value="b" name="q4" required>
                B. FAN
            </label>
        </div>
        <div class="mb-3">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" value="c" name="q4" required>
                C. HARD DRIVE
            </label>
        </div>
        <div class="mb-4">
            <label class="form-check-label">
                <input class="form-check-input" type="radio" value="d" name="q4" required>
                D. OPTICAL DRIVE
            </label>
        </div>

        <button type="submit" name="submit" class="btn btn-success w-100 py-2">Next Question</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<?php
ob_end_flush();
?>