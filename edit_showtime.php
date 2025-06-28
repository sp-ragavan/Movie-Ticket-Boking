<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header("Location: manage_showtimes.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM showtimes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$showtime = $result->fetch_assoc();

// Fetch theaters
$theatersQuery = "SELECT * FROM theaters";
$theatersResult = $conn->query($theatersQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $theater_id = $_POST['theater_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];

    $query = "UPDATE showtimes SET theater_id = ?, show_date = ?, show_time = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issi", $theater_id, $show_date, $show_time, $id);

    if ($stmt->execute()) {
        header("Location: manage_showtimes.php");
    } else {
        $error = "Error updating showtime!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Showtime</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Edit Showtime</h2>
        <form method="POST">
            <select name="theater_id" required>
                <?php while ($theater = $theatersResult->fetch_assoc()) { ?>
                    <option value="<?= $theater['id']; ?>" <?= $theater['id'] == $showtime['theater_id'] ? 'selected' : ''; ?>>
                        <?= $theater['name']; ?>
                    </option>
                <?php } ?>
            </select><br>
            <input type="date" name="show_date" value="<?= $showtime['show_date']; ?>" required>
            <input type="time" name="show_time" value="<?= $showtime['show_time']; ?>" required>
            <button type="submit">Update Showtime</button>
        </form>
    </div>
    <a href="manage_showtimes.php" class="showback">Back</a>
</body>
</html>
