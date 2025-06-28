<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome</h2>
        
            <a href="manage_movies.php">Manage Movies</a><br>
            <a href="manage_theaters.php">Manage Theaters</a><br>
            <a href="manage_showtimes.php">Manage Showtimes</a><br>
            <a href="manage_seats.php">Manage Seats</a><br>
            <a href="manage_canteen.php">Manage Canteen Items</a><br>
        
    </div>
    <a href="admin_logout.php" class="logout">Logout</a>
</body>
</html>
