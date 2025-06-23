<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$records = [];
$username = "";

if (isset($_GET['user']) && !empty($_GET['user'])) {
    $username = $_GET['user'];

    $stmt = $conn->prepare(
        "SELECT r.date, r.score, r.remark 
         FROM results r
         JOIN users u ON r.user_id = u.id
         WHERE u.username = ?
         ORDER BY r.date DESC"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
} else {
    die("No user specified.");
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Record - <?php echo htmlspecialchars($username); ?></title>

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(#f5f5f5, #d3d3d3);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }
        .record-box {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
            width: 600px;
            text-align: center;
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #aaa;
        }
        th {
            background-color: #ffc107;
        }
        td {
            background-color: #f9f9f9;
        }
        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: white;
            background-color: #7d4caf;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-link:hover {
            background-color: #a87ed8;
        }
    </style>
</head>
<body>
    <div class="record-box">
        <h2>Records for <?php echo htmlspecialchars($username); ?></h2>

        <?php if (!empty($records)): ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Remark</th>
                </tr>
                <?php foreach ($records as $rec): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rec['date']); ?></td>
                        <td><?php echo htmlspecialchars($rec['score']); ?></td>
                        <td><?php echo htmlspecialchars($rec['remark']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No records found for this user.</p>
        <?php endif; ?>

        <a href="viewrankings.php" class="back-link"><i class="bi bi-arrow-left-circle"></i> Back to Rankings</a>
    </div>
</body>
</html>
