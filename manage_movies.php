 <?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch movies
$query = "SELECT * FROM movies";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Movies</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .movies img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="movies">
        <h2>Manage Movies</h2>
        <a href="add_movie.php" class="moviebtn">Add Movie</a>
        <table border="1">
            <tr>
                <th>Title</th>
                <th>Release Date</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($movie = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($movie['title']); ?></td>
                    <td><?= htmlspecialchars($movie['release_date']); ?></td>
                    <td>
                        <?php 
                        if (!empty($movie['image'])) {
                            $base64Image = base64_encode($movie['image']);
                            echo '<img src="data:image/jpeg;base64,' . $base64Image . '" alt="Movie Image" />';
                        } else {
                            echo '<img src="default.jpg" alt="Default Image" />';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="edit_movie.php?id=<?= $movie['id']; ?>">Edit</a> | 
                        <a href="delete_movie.php?id=<?= $movie['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <a href="admin_dashboard.php" class="movieback">Back to Dashboard</a>
</body>
</html>
