<?php
// viewrankings.php - Complete Quiz Rankings Page with Bulk Delete Functionality

$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";
$conn = mysqli_connect($host, $user, $pass, $db);

$searchTerm = "";
$results = [];

// Handle bulk delete
if (isset($_POST['delete_selected']) && isset($_POST['selected_records'])) {
    $selectedIds = $_POST['selected_records'];
    if (!empty($selectedIds)) {
        $placeholders = str_repeat('?,', count($selectedIds) - 1) . '?';
        $deleteStmt = $conn->prepare("DELETE FROM results WHERE id IN ($placeholders)");
        $deleteStmt->bind_param(str_repeat('i', count($selectedIds)), ...$selectedIds);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Redirect to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . (!empty($_GET['search']) ? "?search=" . urlencode($_GET['search']) : ""));
        exit();
    }
}

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
            width: 950px;
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
        .delete-selected-btn {
            margin-top: 20px;
            margin-right: 10px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-selected-btn:hover {
            background-color: #c82333;
        }
        .delete-selected-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
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
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .checkbox-column {
            width: 50px;
        }
    </style>
</head>
<body>
    <div class="results-box">
        <h2>Leaderboard</h2>
        <div class="timestamp">Today is <?php echo date("F j, Y, g:i a"); ?></div>

        <!-- Search Form -->
        <form method="get" class="search-form">
            <input type="text" name="search" class="form-control w-50" placeholder="Search username..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        </form>

        <!-- Main Form for Bulk Operations -->
        <form method="post" id="bulkForm">
            <table>
                <tr>
                    <th class="checkbox-column">
                        <input type="checkbox" id="checkAll" title="Check/Uncheck All">
                    </th>
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
                                <td class='checkbox-column'>
                                    <input type='checkbox' name='selected_records[]' value='" . $row['id'] . "' class='record-checkbox'>
                                </td>
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
                    echo "<tr><td colspan='7'>No records found.</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </table>

            <div class="button-container">
                <button type="submit" name="delete_selected" class="delete-selected-btn" id="deleteSelectedBtn" disabled onclick="return confirm('Are you sure you want to delete the selected records?')">
                    <i class="bi bi-trash"></i> Delete Selected
                </button>
                
                <?php if (!empty($searchTerm)): ?>
                    <a href="viewrankings.php" class="back-button"><i class="bi bi-arrow-left-circle"></i> Back to All Records</a>
                <?php else: ?>
                    <a href="result.php" class="back-button"><i class="bi bi-arrow-left-circle"></i> Back</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        // Check/Uncheck all functionality
        document.getElementById('checkAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.record-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });

        // Individual checkbox change handler
        document.querySelectorAll('.record-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCheckAllState();
                updateDeleteButton();
            });
        });

        // Update the "Check All" checkbox state based on individual checkboxes
        function updateCheckAllState() {
            const checkboxes = document.querySelectorAll('.record-checkbox');
            const checkAllBox = document.getElementById('checkAll');
            const checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
            
            if (checkedCount === 0) {
                checkAllBox.indeterminate = false;
                checkAllBox.checked = false;
            } else if (checkedCount === checkboxes.length) {
                checkAllBox.indeterminate = false;
                checkAllBox.checked = true;
            } else {
                checkAllBox.indeterminate = true;
            }
        }

        // Enable/disable delete button based on selection
        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            deleteBtn.disabled = checkedBoxes.length === 0;
        }

        // Initialize button state on page load
        updateDeleteButton();
    </script>
</body>
</html>