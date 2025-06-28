<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_theaters.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM theaters WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$theater = $result->fetch_assoc();

// Fetch movies for selection
$moviesQuery = "SELECT * FROM movies";
$moviesResult = $conn->query($moviesQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $movie_id = $_POST['movie_id'];

    $query = "UPDATE theaters SET name = ?, location = ?, movie_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $name, $location, $movie_id, $id);

    if ($stmt->execute()) {
        header("Location: manage_theaters.php");
    } else {
        $error = "Error updating theater!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Theater</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Edit Theater</h2>
        <form method="POST">
            <input type="text" name="name" value="<?= $theater['name']; ?>" required>
            <input type="text" name="location" value="<?= $theater['location']; ?>" required>
            <select name="movie_id" required>
                <?php while ($movie = $moviesResult->fetch_assoc()) { ?>
                    <option value="<?= $movie['id']; ?>" <?= $movie['id'] == $theater['movie_id'] ? 'selected' : ''; ?>>
                        <?= $movie['title']; ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit">Update Theater</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <a href="manage_theaters.php" class="theaterback">Back</a>
</body>
</html>
