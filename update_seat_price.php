<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showtime_id = $_POST['showtime_id'];
    $theater_id = $_POST['theater_id'];
    $seat_numbers = $_POST['seat_numbers'];
    $prices = $_POST['prices'];

    if (count($seat_numbers) === count($prices)) {
        for ($i = 0; $i < count($seat_numbers); $i++) {
            $seat_number = $seat_numbers[$i];
            $price = floatval($prices[$i]);

            // Update or Insert seat price
            $query = "INSERT INTO seats (showtime_id, theater_id, seat_number, price) 
                      VALUES (?, ?, ?, ?) 
                      ON DUPLICATE KEY UPDATE price = VALUES(price)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iisd", $showtime_id, $theater_id, $seat_number, $price);
            $stmt->execute();
        }
    }

    header("Location: manage_seats.php?theater_id=$theater_id&showtime_id=$showtime_id");
    exit();
}
?>
