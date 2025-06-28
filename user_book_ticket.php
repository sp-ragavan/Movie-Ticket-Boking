<?php
session_start();
include 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['showtime_id']) || !isset($_POST['selected_seat'])) {
    header("Location: user_theaters.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$showtime_id = $_POST['showtime_id'];
$selected_seats = explode(", ", $_POST['selected_seat']);

// Fetch theater ID from the showtime
$theaterQuery = "SELECT theater_id FROM showtimes WHERE id = ?";
$stmt = $conn->prepare($theaterQuery);
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$theaterResult = $stmt->get_result();
$theaterRow = $theaterResult->fetch_assoc();
$theater_id = $theaterRow['theater_id'];

// Fetch canteen menu items for the theater
$menuQuery = "SELECT * FROM canteen_menu WHERE theater_id = ?";
$stmt = $conn->prepare($menuQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$menuResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Ticket</title>
    <link rel="stylesheet" href="user_canteen.css">

</head>
<body>
    <div class="container">
        <h2>Confirm Your Booking</h2>
        <form method="POST" action="process_booking.php">
            <input type="hidden" name="showtime_id" value="<?= $showtime_id; ?>">
            <input type="hidden" name="selected_seat" value="<?= htmlspecialchars($_POST['selected_seat']); ?>">
            
            <h3>Selected Seats:</h3>
            <p><?= htmlspecialchars($_POST['selected_seat']); ?></p>
            
            <h3>Order Canteen Items (Optional)</h3>
            <?php if ($menuResult->num_rows > 0) { ?>
                <?php while ($item = $menuResult->fetch_assoc()) { ?>
                    <div class="canteen-item">
    <label>
    <?= htmlspecialchars($item['item_name']) ?> - ₹<?= htmlspecialchars($item['price']); ?>
        <input type="checkbox" name="canteen_items[]" value="<?= $item['id']; ?>">
    </label>
</div>

                <?php } ?>
            <?php } else { ?>
                <p>No canteen items available for this theater.</p>
            <?php } ?>

            <button type="submit">Confirm Booking</button>
        </form>
        <a href="user_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

