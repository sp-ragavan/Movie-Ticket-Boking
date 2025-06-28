<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch movies for selection
$moviesQuery = "SELECT * FROM movies";
$moviesResult = $conn->query($moviesQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $movie_id = $_POST['movie_id'];

    $query = "INSERT INTO theaters (name, location, movie_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $name, $location, $movie_id);
    
    if ($stmt->execute()) {
        header("Location: manage_theaters.php");
    } else {
        $error = "Error adding theater!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Theater</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Add Theater</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Theater Name" required>
            <input type="text" name="location" placeholder="Location" required>
            <select name="movie_id" required>
                <option value="">Select Movie</option>
                <?php while ($movie = $moviesResult->fetch_assoc()) { ?>
                    <option value="<?= $movie['id']; ?>"><?= $movie['title']; ?></option>
                <?php } ?>
            </select>
            <button type="submit">Add Theater</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <a href="manage_theaters.php" class="theaterback">Back</a>
</body>
</html>
