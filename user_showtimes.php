<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get theater ID from URL
if (!isset($_GET['theater_id'])) {
    header("Location: user_theaters.php");
    exit();
}

$theater_id = $_GET['theater_id'];

// Fetch theater details
$theaterQuery = "SELECT * FROM theaters WHERE id = ?";
$stmt = $conn->prepare($theaterQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$theaterResult = $stmt->get_result();
$theater = $theaterResult->fetch_assoc();

// Fetch showtimes for this theater
$showtimeQuery = "SELECT * FROM showtimes WHERE theater_id = ?";
$stmt = $conn->prepare($showtimeQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$showtimeResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Showtime</title>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="container">
        <h2>Select a Showtime at <?= htmlspecialchars($theater['name']); ?></h2>
        <div class="showtimes">
            <?php while ($showtime = $showtimeResult->fetch_assoc()) { ?>
                <div class="showtime">
                    <p><strong>Date:</strong> <?= htmlspecialchars($showtime['show_date']); ?></p>
                    <p><strong>Time:</strong> <?= date("h:i A", strtotime($showtime['show_time'])); ?></p>
                    <a href="user_seats.php?theater_id=<?= $theater_id; ?>&showtime_id=<?= $showtime['id']; ?>" class="btn">Select</a>
                </div>
            <?php } ?>
        </div>
        <a href="user_theaters.php" class="back-btn">Back</a>
    </div>
</body>
</html>
