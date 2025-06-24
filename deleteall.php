<?php
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM results");
    header("Location: viewrankings.php");
    exit;
}
?>
<a href="?delete_all=1" class="btn btn-danger mb-3" onclick="return confirm('Are you sure you want to delete ALL records?')">
    <i class="bi bi-trash-fill"></i> Delete All
</a>
