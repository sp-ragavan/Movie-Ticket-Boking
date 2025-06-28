<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_movies.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];

    if (!empty($_FILES["image"]["tmp_name"])) {
        $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
        $query = "UPDATE movies SET title = ?, release_date = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $null = NULL; // placeholder for blob
        $stmt->bind_param("ssbi", $title, $release_date, $null, $id);
        $stmt->send_long_data(2, $imageData);
    } else {
        $query = "UPDATE movies SET title = ?, release_date = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $title, $release_date, $id);
    }

    if ($stmt->execute()) {
        header("Location: manage_movies.php");
        exit();
    } else {
        $error = "Error updating movie!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Movie</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Edit Movie</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" value="<?= htmlspecialchars($movie['title']); ?>" required>
            <input type="date" name="release_date" value="<?= htmlspecialchars($movie['release_date']); ?>" required>
            <input type="file" name="image">
            <button type="submit">Update Movie</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <a href="manage_movies.php" class="movieback">Back</a>
</body>
</html>
