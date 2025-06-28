<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$query = "SELECT * FROM movies";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .dashboard {
            position: relative;
            width: 90%;
            margin: 5px auto;
            text-align: center;
        }
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px 20px;
        }
        .top-left {
            position: absolute;
            left: 0px;
            top: 10px;
        }
        .center-top {
            margin-top: 10px;
        }
        .movies-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .movie-box {
            width: 150px;
            margin: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .movie-box img {
            width: 100%;
            height: 300px;
            border-radius: 10px;
            object-fit: cover;
        }
        a {
    text-decoration: none;  /* Removes underline */
    color: skyblue;       /* Change to your preferred color */
    font-family: Arial, sans-serif; /* Change font style */
    font-weight: bold;       /* Make text bold */
    font-size: 16px;         /* Adjust font size */
}

a:hover {
    color:rgb(1, 41, 172);  /* Change color on hover */
}
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?= $_SESSION['user_name']; ?></h1>

        <div class="top-nav">
            <a href="user_booking_history.php" class="top-left">Booking History</a>
        </div>

        <div class="center-top">
            <h3  class="book-movie">Book a Movie</h3>
        </div>
    </div>
    <div class="movies-container">
        <?php while ($movie = $result->fetch_assoc()) { ?>
    <a class="movie-box" href="user_theaters.php?movie_id=<?= $movie['id']; ?>">
        <img src="data:image/jpeg;base64,<?= base64_encode($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
        <h3><?= htmlspecialchars($movie['title']); ?></h3>
        <p>Release Date: <?= htmlspecialchars($movie['release_date']); ?></p>
    </a>
<?php } ?>

    </div>
    <a href="user_logout.php" class="logout">Logout</a>
</body>
</html>





