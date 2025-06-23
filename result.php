<?php
ob_start();
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "quiz_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['username']) || $_SESSION['username'] === 'Guest') {
    header("Location: login.php");
    exit();
}


$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
   
    try {
        $insert_user = $pdo->prepare("INSERT INTO users (username) VALUES (?)");
        $insert_user->execute([$_SESSION['username']]);
        $user_id = $pdo->lastInsertId();
    } catch(PDOException $e) {
        die("Error creating user: " . $e->getMessage());
    }
} else {
    $user_id = $user_data['id'];
}


$has_fresh_quiz_data = isset($_SESSION['q1']) || isset($_SESSION['q2']) || isset($_SESSION['q3']) || 
                       isset($_SESSION['q4']) || isset($_SESSION['q5']) || isset($_SESSION['q6']) || 
                       isset($_SESSION['q7']) || isset($_SESSION['q8']) || isset($_SESSION['q9']) || 
                       isset($_SESSION['q10']);

$score = 0;
$total_questions = 10;
$show_save_message = false;

$correct_answers = [
    'q1' => 'd', 'q2' => 'b', 'q3' => 'd', 'q4' => 'c', 'q5' => 'b', 
    'q6' => 'c', 'q7' => 'a', 'q8' => 'b', 'q9' => 'c', 'q10' => 'b'
];


$questions = [
    'q1' => '1. What is the name of this symbol? (Power Symbol)',
    'q2' => '2. What does CPU stand for?',
    'q3' => '3. What is the name of this part? (RAM Image)',
    'q4' => '4. What is the name of this part? (Hard Drive Image)',
    'q5' => '5. What is the name of a system unit? (Mini Tower Image)',
    'q6' => '6. What does USB stand for?',
    'q7' => '7. JavaScript can be used to build websites?',
    'q8' => '8. HTML is a programming language?',
    'q9' => '9. Which Language is this code snippet written in? print("Hello, World!")',
    'q10' => '10. What is the name of this symbol? (Wi-Fi Symbol)'
];


$answer_options = [
    'q1' => ['a' => 'A. (Option A Text)', 'b' => 'B. (Option B Text)', 'c' => 'C. (Option C Text)', 'd' => 'D. Gigabyte'],
    'q2' => ['a' => 'A. Computer Personal Unit', 'b' => 'B. Central Processing Unit', 'c' => 'C. Control Panel Unit', 'd' => 'D. Core Processing Unit'],
    'q3' => ['a' => 'A. SSD', 'b' => 'B. PSU', 'c' => 'C. CPU', 'd' => 'D. RAM'],
    'q4' => ['a' => 'A. CPU', 'b' => 'B. FAN', 'c' => 'C. HARD DRIVE', 'd' => 'D. OPTICAL DRIVE'],
    'q5' => ['a' => 'A. Slim Tower', 'b' => 'B. Mini Tower', 'c' => 'C. Full Tower', 'd' => 'D. Small form factor'],
    'q6' => ['a' => 'A. Unique System Board', 'b' => 'B. Ultra Storage', 'c' => 'C. Universal Serial Bus', 'd' => 'D. Universal Series Bus'],
    'q7' => ['a' => 'True', 'b' => 'False'],
    'q8' => ['a' => 'True', 'b' => 'False'],
    'q9' => ['a' => 'A. Java', 'b' => 'B. C++', 'c' => 'C. Python', 'd' => 'D. PHP'],
    'q10' => ['a' => 'A. Bluetooth Symbol', 'b' => 'B. Wi-Fi Symbol', 'c' => 'C. Ethernet Symbol', 'd' => 'D. Mobile Data Symbol']
];

$user_answers = [];
$question_statuses = [];
$display_answers = [];

if ($has_fresh_quiz_data) {

    $show_save_message = true;
    
    foreach ($correct_answers as $question => $correct_answer) {
        $user_answer = $_SESSION[$question] ?? null;
        $user_answers[$question] = $user_answer;
        
        if ($user_answer !== null) {
            if (strtolower($user_answer) == strtolower($correct_answer)) {
                $score++;
                $question_statuses[$question] = 'Correct';
            } else {
                $question_statuses[$question] = 'Wrong';
            }
            
   
            if (isset($answer_options[$question][strtolower($user_answer)])) {
                $display_answers[$question] = $answer_options[$question][strtolower($user_answer)];
            } else {
                $display_answers[$question] = strtoupper($user_answer);
            }
        } else {
            $question_statuses[$question] = 'Wrong';
            $display_answers[$question] = 'Not Answered';
        }
    }
   
    $passing_score = ceil($total_questions * 0.6);
    $remark = '';
    if ($score === $total_questions) {
        $remark = 'Perfect Score!';
    } elseif ($score >= $passing_score) {
        $remark = 'Passed';
    } else {
        $remark = 'Failed';
    }
    
    
    try {
       
        $check_stmt = $pdo->prepare("SELECT id FROM results WHERE user_id = ? ORDER BY date DESC LIMIT 1");
        $check_stmt->execute([$user_id]);
        $existing_result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_result) {
           
            $update_stmt = $pdo->prepare("UPDATE results SET score = ?, remark = ?, date = CURRENT_TIMESTAMP WHERE id = ?");
            $update_stmt->execute([$score, $remark, $existing_result['id']]);
            $result_id = $existing_result['id'];
        } else {
            
            $insert_stmt = $pdo->prepare("INSERT INTO results (user_id, score, remark) VALUES (?, ?, ?)");
            $insert_stmt->execute([$user_id, $score, $remark]);
            $result_id = $pdo->lastInsertId();
        }
        
        
        $_SESSION['quiz_completed'] = true;
        $_SESSION['last_score'] = $score;
        $_SESSION['last_remark'] = $remark;
        
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
    
   
    unset($_SESSION['q1'], $_SESSION['q2'], $_SESSION['q3'], $_SESSION['q4'], $_SESSION['q5'], 
          $_SESSION['q6'], $_SESSION['q7'], $_SESSION['q8'], $_SESSION['q9'], $_SESSION['q10']);
          
} else {

    $stmt = $pdo->prepare("SELECT score, remark, date FROM results WHERE user_id = ? ORDER BY date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $score = $result['score'];
        $remark = $result['remark'];
        
       
        for ($i = 1; $i <= 10; $i++) {
            $question_statuses["q$i"] = 'Unknown';
            $display_answers["q$i"] = 'Previous Result';
            $user_answers["q$i"] = null;
        }
    } else {
     
        header("Location: quiz.php");
        exit();
    }
}

$passing_score = ceil($total_questions * 0.6);
$result_message_class = ($score >= $passing_score) ? 'passed' : 'failed';
$result_text = ($score === $total_questions) ? 'Perfect 10/10!' : (($score >= $passing_score) ? 'Passed' : 'Failed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(249, 224, 250);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .results-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 650px;
            text-align: center;
            border-top: 5px solid #7d4caf;
            margin-bottom: 20px;
        }

        .user-message {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .score-display {
            font-size: 2.2em;
            color: #7d4caf;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .result-message {
            font-size: 1.8em;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .passed {
            background-color: #e8f5e9;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }

        .failed {
            background-color: #ffebee;
            color: #D32F2F;
            border: 2px solid #D32F2F;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 0.95em;
        }

        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .results-table th {
            background-color: #f2f2f2;
            color: #555;
            font-weight: bold;
        }

        .results-table td.correct {
            color: #28a745;
            font-weight: bold;
        }

        .results-table td.wrong {
            color: #dc3545;
            font-weight: bold;
        }

        .results-table td.unknown {
            color: #6c757d;
            font-style: italic;
        }

        .logout-button {
            background-color: #7d4caf;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .logout-button:hover {
            background-color: rgb(219, 183, 255);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .info-message {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
<div class="results-container">
    <?php if ($show_save_message): ?>
        <div class="success-message">
            Your quiz results have been saved successfully!
        </div>
    <?php else: ?>
        <div class="info-message">
            Showing your latest quiz results from the database.
        </div>
    <?php endif; ?>
    
    <div class="user-message">
        <?php echo "Welcome, " . htmlspecialchars($_SESSION['username'] ?? 'Guest') . "!"; ?>
    </div>
    <div class="score-display">Your Score: <?php echo $score; ?> / <?php echo $total_questions; ?></div>
    <div class="result-message <?php echo $result_message_class; ?>"><?php echo $result_text; ?></div>

    <table class="results-table">
        <thead>
            <tr>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <tr>
                <td><?php echo htmlspecialchars($questions["q$i"]); ?></td>
                <td class="<?php echo strtolower($question_statuses["q$i"]); ?>"><?php echo $display_answers["q$i"]; ?></td>
                <td class="<?php echo strtolower($question_statuses["q$i"]); ?>"><?php echo $question_statuses["q$i"]; ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>
<div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
    <a href="viewrankings.php" class="logout-button">View Rankings</a>
    <a href="logout.php" class="logout-button">Logout</a>
</div>

</body>
</html>