<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch showtimes from the database
$query = "SELECT showtimes.id, theaters.name AS theater_name, 
                 showtimes.show_date, showtimes.show_time 
          FROM showtimes 
          JOIN theaters ON showtimes.theater_id = theaters.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Showtimes</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="show">
        <h2>Manage Showtimes</h2>
        <a href="add_showtime.php" class="showbtn">Add Showtime</a>
        <table border="1">
            <tr>
                <th>Theater</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
            <?php while ($showtime = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $showtime['theater_name']; ?></td>
                    <td><?= $showtime['show_date']; ?></td>
                    <td><?= $showtime['show_time']; ?></td>
                    <td>
                        <a href="edit_showtime.php?id=<?= $showtime['id']; ?>">Edit</a> | 
                        <a href="delete_showtime.php?id=<?= $showtime['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <a href="admin_dashboard.php" class="showback">Back to Dashboard</a>
</body>
</html>

