<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "quiz_db";
$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM results WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Record Deleted!!!'); window.location.href='viewrankings.php';</script>";
}

mysqli_close($conn);
?>
