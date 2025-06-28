<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get movie ID from URL
if (!isset($_GET['movie_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$movie_id = $_GET['movie_id'];

// Fetch movie details
$movieQuery = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($movieQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movieResult = $stmt->get_result();
$movie = $movieResult->fetch_assoc();

// Fetch theaters showing this movie
$theaterQuery = "SELECT * FROM theaters WHERE movie_id = ?";
$stmt = $conn->prepare($theaterQuery);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$theaterResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Theater</title>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="container">
        <h2>Select a Theater for <br> "<?= $movie['title']; ?>"</h2>
        <div class="theaters">
            <?php while ($theater = $theaterResult->fetch_assoc()) { ?>
                <div class="theater">
                    <h3><?= $theater['name']; ?></h3>
                    <p>Location: <?= $theater['location']; ?></p>
                    <a href="user_showtimes.php?movie_id=<?= $movie_id; ?>&theater_id=<?= $theater['id']; ?>" class="btn">Select</a>
                </div>
            <?php } ?>
        </div>
        <a href="user_dashboard.php" class="back-btn">Back</a>
    </div>
</body>
</html>
