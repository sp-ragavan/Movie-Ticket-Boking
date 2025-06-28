<?php
session_start();
include 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate input
if (!isset($_POST['showtime_id']) || !isset($_POST['selected_seat'])) {
    die("Error: Invalid booking request.");
}

$user_id = $_SESSION['user_id'];
$showtime_id = $_POST['showtime_id'];
$selected_seats = explode(", ", $_POST['selected_seat']);
$canteen_items = isset($_POST['canteen_items']) ? $_POST['canteen_items'] : [];

$conn->begin_transaction();

try {
    foreach ($selected_seats as $seat) {
        // Get seat ID and associated movie ID
        $seatCheckQuery = "SELECT seats.id, seats.is_booked, movies.id AS movie_id 
                           FROM seats 
                           JOIN theaters ON seats.theater_id = theaters.id
                           JOIN movies ON theaters.movie_id = movies.id
                           WHERE seats.seat_number = ? AND seats.showtime_id = ?";
        $stmt = $conn->prepare($seatCheckQuery);
        $stmt->bind_param("si", $seat, $showtime_id);
        $stmt->execute();
        $seatResult = $stmt->get_result();

        if ($seatResult->num_rows === 0) {
            throw new Exception("Seat $seat not found.");
        }

        $seatRow = $seatResult->fetch_assoc();
        if ($seatRow['is_booked'] == 1) {
            throw new Exception("Seat $seat is already booked.");
        }

        $seat_id = $seatRow['id'];
        $movie_id = $seatRow['movie_id']; // Fetch movie_id from the movies table

        // Mark seat as booked
        $updateSeatQuery = "UPDATE seats SET is_booked = 1 WHERE id = ?";
        $stmt = $conn->prepare($updateSeatQuery);
        $stmt->bind_param("i", $seat_id);
        $stmt->execute();

        // Insert booking details (Now including movie_id and booking_date)
        $bookingQuery = "INSERT INTO bookings (user_id, showtime_id, movie_id, seat_id, booking_date) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($bookingQuery);
        $stmt->bind_param("iiii", $user_id, $showtime_id, $movie_id, $seat_id);
        $stmt->execute();
        
        // Get last inserted booking ID
        $booking_id = $conn->insert_id;

        // Insert canteen orders linked to booking_id
        if (!empty($canteen_items)) {
            foreach ($canteen_items as $item_id) {
                $canteenQuery = "INSERT INTO booked_canteen_items (booking_id, item_id) VALUES (?, ?)";
                $stmt = $conn->prepare($canteenQuery);
                $stmt->bind_param("ii", $booking_id, $item_id);
                $stmt->execute();
            }
        }
    }

    // Commit transaction
    $conn->commit();
    echo "<script>
            alert('Booking Successful!');
            window.location.href = 'user_dashboard.php';
          </script>";
    exit();
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>
            alert('Booking Failed! " . addslashes($e->getMessage()) . "');
            window.location.href = 'user_dashboard.php';
          </script>";
}
?>

