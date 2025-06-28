<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch theaters from the database
$query = "SELECT theaters.id, theaters.name, theaters.location, movies.title AS movie_title 
          FROM theaters 
          JOIN movies ON theaters.movie_id = movies.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Theaters</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="theater">
        <h2>Manage Theaters</h2>
        <a href="add_theater.php" class="theaterbtn">Add Theater</a>
        <table border="1">
            <tr>
                <th>Theater Name</th>
                <th>Location</th>
                <th>Movie</th>
                <th>Actions</th>
            </tr>
            <?php while ($theater = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $theater['name']; ?></td>
                    <td><?= $theater['location']; ?></td>
                    <td><?= $theater['movie_title']; ?></td>
                    <td>
                        <a href="edit_theater.php?id=<?= $theater['id']; ?>">Edit</a> | 
                        <a href="delete_theater.php?id=<?= $theater['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <a href="admin_dashboard.php" class="theaterback">Back to Dashboard</a>
</body>
</html>
