<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";
$conn = mysqli_connect($host, $user, $pass, $db);

$searchTerm = "";
$results = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    $stmt = $conn->prepare(
        "SELECT r.id, u.username, r.score, r.date, r.remark 
         FROM results r 
         JOIN users u ON r.user_id = u.id 
         WHERE u.username LIKE ?
         ORDER BY r.score DESC"
    );
    $searchWildcard = "%{$searchTerm}%";
    $stmt->bind_param("s", $searchWildcard);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT r.id, u.username, r.score, r.date, r.remark 
              FROM results r 
              JOIN users u ON r.user_id = u.id 
              ORDER BY r.score DESC";
    $result = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Rankings</title>


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
        .results-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
            width: 900px;
            text-align: center;
        }
        .timestamp {
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #aaa;
        }
        th {
            background-color: #f1c40f;
        }
        td {
            background-color: #f9f9f9;
        }
        .btn-sm {
            font-size: 14px;
            padding: 5px 10px;
        }
        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #7d4caf;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #a87ed8;
        }
        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        .username-link {
            text-decoration: none;
            color: #007bff;
        }
        .username-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="results-box">
        <h2>Leaderboard</h2>
        <div class="timestamp">Today is <?php echo date("F j, Y, g:i a"); ?></div>

       
        <form method="get" class="search-form">
            <input type="text" name="search" class="form-control w-50" placeholder="Search username..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        </form>

        <table>
            <tr>
                <th>Date</th>
                <th>Username</th>
                <th>Score</th>
                <th>Remark</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td><a href='viewpage.php?user=" . urlencode($row['username']) . "' class='username-link'>" . htmlspecialchars($row['username']) . "</a></td>
                            <td>" . htmlspecialchars($row['score']) . "</td>
                            <td>" . htmlspecialchars($row['remark']) . "</td>
                            <td>
                                <a href='edit.php?user=" . urlencode($row['id']) . "' class='btn btn-primary btn-sm' title='Edit'>
                                    <i class='bi bi-pencil-square'></i>
                                </a>
                            </td>
                            <td>
                                <a href='delete.php?id=" . urlencode($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this record?\")' title='Delete'>
                                    <i class='bi bi-trash'></i>
                                </a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No records found.</td></tr>";
            }
            mysqli_close($conn);
            ?>
        </table>

 
        <?php if (!empty($searchTerm)): ?>
            <a href="viewrankings.php" class="back-button"><i class="bi bi-arrow-left-circle"></i> Back to All Records</a>
        <?php else: ?>
            <a href="result.php" class="back-button"><i class="bi bi-arrow-left-circle"></i> Back</a>
        <?php endif; ?>
    </div>
</body>
</html>
