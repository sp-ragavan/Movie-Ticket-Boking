<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch theaters
$theatersQuery = "SELECT * FROM theaters";
$theatersResult = $conn->query($theatersQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $theater_id = $_POST['theater_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];

    $query = "INSERT INTO showtimes (theater_id, show_date, show_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $theater_id, $show_date, $show_time);

    if ($stmt->execute()) {
        header("Location: manage_showtimes.php");
    } else {
        $error = "Error adding showtime!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Showtime</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Add Showtime</h2>
        <form method="POST">
            <select name="theater_id" required>
                <option value="">Select Theater</option>
                <?php while ($theater = $theatersResult->fetch_assoc()) { ?>
                    <option value="<?= $theater['id']; ?>"><?= $theater['name']; ?></option>
                <?php } ?>
            </select><br>
            <input type="date" name="show_date" required><br>
            <input type="time" name="show_time" required>
            <button type="submit">Add Showtime</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <a href="manage_showtimes.php" class="showback">Back</a>
</body>
</html>
