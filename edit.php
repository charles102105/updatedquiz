<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$record = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $result_id = intval($_POST['result_id']);
    $date = $_POST['date'];
    $score = intval($_POST['score']);

  
    $remark = ($score < 5) ? 'Failed' : 'Passed';

    $stmt = $conn->prepare("UPDATE results SET date = ?, score = ?, remark = ? WHERE id = ?");
    $stmt->bind_param("sisi", $date, $score, $remark, $result_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully'); window.location.href='viewrankings.php';</script>";
    } else {
        echo "<script>alert('Update failed: " . $stmt->error . "');</script>";
    }
    $stmt->close();
} elseif (isset($_GET['user'])) {
    $result_id = intval($_GET['user']);

    $stmt = $conn->prepare(
        "SELECT r.id AS result_id, r.date, r.score, r.remark, u.id AS user_id, u.username
         FROM results r
         JOIN users u ON r.user_id = u.id
         WHERE r.id = ?"
    );
    $stmt->bind_param("i", $result_id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    die("Invalid access.");
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(#eeeeee, #d3d3d3);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 60px;
        }
        .form-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
            width: 400px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #aaa;
            background-color: white;
        }
        input[readonly] {
            background-color: #f3f3f3;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #1d6fa5;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit Result</h2>
        <?php if (!empty($record)): ?>
        <form method="post">
            <input type="hidden" name="result_id" value="<?php echo htmlspecialchars($record['result_id']); ?>">

            <label>Date:</label><br>
            <input type="text" name="date" value="<?php echo htmlspecialchars($record['date']); ?>" required><br>

            <label>Score:</label><br>
            <input type="text" name="score" value="<?php echo htmlspecialchars($record['score']); ?>" required><br>

            <label>Username:</label><br>
            <input type="text" value="<?php echo htmlspecialchars($record['username']); ?>" readonly><br>

            <label>Remark:</label><br>
            <input type="text" value="<?php echo ($record['score'] < 5) ? 'Failed' : 'Passed'; ?>" readonly><br>

            <input type="submit" name="update" value="Update Record">
        </form>
        <?php else: ?>
            <p>No record found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
