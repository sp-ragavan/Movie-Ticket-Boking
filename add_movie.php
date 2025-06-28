
 <?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];

    // Read image as binary
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageData = file_get_contents($_FILES["image"]["tmp_name"]);

        $query = "INSERT INTO movies (title, release_date, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Use "s" for string and "b" for blob
        $stmt->bind_param("ssb", $title, $release_date, $null);
        $stmt->send_long_data(2, $imageData);

        if ($stmt->execute()) {
            header("Location: manage_movies.php");
            exit();
        } else {
            $error = "Error adding movie!";
        }

        $stmt->close();
    } else {
        $error = "Error uploading image!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Movie</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Add Movie</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Movie Title" required>
            <input type="date" name="release_date" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Add Movie</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <a href="manage_movies.php" class="movieback">Back</a>
</body>
</html>
