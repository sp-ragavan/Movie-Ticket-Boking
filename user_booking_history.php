<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Modified query with seat join
$query = "SELECT b.id, m.title, t.name AS theater, s.show_date, s.show_time, seats.seat_number
          FROM bookings b
          JOIN showtimes s ON b.showtime_id = s.id
          JOIN movies m ON b.movie_id = m.id
          JOIN theaters t ON s.theater_id = t.id
          JOIN seats ON b.seat_id = seats.id
          WHERE b.user_id = ?
          ORDER BY s.show_date DESC, s.show_time DESC";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking History</title>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="container">
        <h2>Your Booking History</h2>
        <?php if ($result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Movie</th>
                    <th>Theater</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Seat</th>
                    <th>Canteen Items</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['theater']); ?></td>
                        <td><?= htmlspecialchars($row['show_date']); ?></td>
                        <td><?= htmlspecialchars($row['show_time']); ?></td>
                        <td><?= htmlspecialchars($row['seat_number']); ?></td>
                        <td>
                            <?php
                            // Fetch booked canteen items for this booking
                            $canteenQuery = "SELECT c.item_name FROM booked_canteen_items bci
                                             JOIN canteen_menu c ON bci.item_id = c.id
                                             WHERE bci.booking_id = ?";
                            $stmtC = $conn->prepare($canteenQuery);
                            $stmtC->bind_param("i", $row['id']);
                            $stmtC->execute();
                            $canteenResult = $stmtC->get_result();

                            // Display canteen items if any are found
                            if ($canteenResult->num_rows > 0) {
                                while ($item = $canteenResult->fetch_assoc()) {
                                    echo htmlspecialchars($item['item_name']) . "<br>";
                                }
                            } else {
                                echo "No items ordered.";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No bookings found.</p>
        <?php } ?>
        <a href="user_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
