<?php
if (isset($_POST['delete_selected']) && !empty($_POST['delete_ids'])) {
    $ids = implode(",", array_map('intval', $_POST['delete_ids']));
    mysqli_query($conn, "DELETE FROM results WHERE id IN ($ids)");
    header("Location: viewrankings.php");
    exit;
}
<form method="post" action="">
<table>
    <tr>
        <th><input type="checkbox" id="selectAll"></th>
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
                    <td><input type='checkbox' name='delete_ids[]' value='{$row['id']}'></td>
                    <td>" . htmlspecialchars($row['date']) . "</td>
                    <td><a href='viewpage.php?user=" . urlencode($row['username']) . "' class='username-link'>" . htmlspecialchars($row['username']) . "</a></td>
                    <td>" . htmlspecialchars($row['score']) . "</td>
                    <td>" . htmlspecialchars($row['remark']) . "</td>
                    <td>
                        <a href='edit.php?user=" . urlencode($row['id']) . "' class='btn btn-primary btn-sm'>
                            <i class='bi bi-pencil-square'></i>
                        </a>
                    </td>
                    <td>
                        <a href='delete.php?id=" . urlencode($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>
                            <i class='bi bi-trash'></i>
                        </a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No records found.</td></tr>";
    }
    ?>
</table>
<button type="submit" name="delete_selected" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to delete selected records?')">Delete Selected</button>
</form>
<script>
document.getElementById('selectAll').addEventListener('click', function () {
    let checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
    for (let checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
});
</script>
