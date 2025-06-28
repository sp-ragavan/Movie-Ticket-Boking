<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get theater and showtime details from URL
if (!isset($_GET['theater_id']) || !isset($_GET['showtime_id'])) {
    header("Location: user_theaters.php");
    exit();
}

$theater_id = $_GET['theater_id'];
$showtime_id = $_GET['showtime_id'];

// Fetch theater details
$stmt = $conn->prepare("SELECT name, total_rows, total_columns FROM theaters WHERE id = ?");
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$theaterResult = $stmt->get_result();
$theater = $theaterResult->fetch_assoc();

// Fetch seats with prices in the correct order
$stmt = $conn->prepare("SELECT seat_number, is_booked, price FROM seats WHERE theater_id = ? AND showtime_id = ? ORDER BY seat_number ASC");
$stmt->bind_param("ii", $theater_id, $showtime_id);
$stmt->execute();
$seatResult = $stmt->get_result();

$seats = [];
while ($seat = $seatResult->fetch_assoc()) {
    $seats[$seat['seat_number']] = $seat;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Seat</title>
    <link rel="stylesheet" href="user_style.css">
    <script>
        let selectedSeats = [];
        let totalPrice = 0;
        
        function selectSeat(seatNumber, price) {
            let seatButton = document.getElementById(seatNumber);
            if (selectedSeats.includes(seatNumber)) {
                selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
                totalPrice -= parseFloat(price);
                seatButton.classList.remove('selected');
            } else {
                selectedSeats.push(seatNumber);
                totalPrice += parseFloat(price);
                seatButton.classList.add('selected');
            }
            document.getElementById('selected_seat').value = selectedSeats.join(', ');
            document.getElementById('seat_price').innerText = "Total Price: ₹" + totalPrice.toFixed(2);
            document.getElementById('seat_count').innerText = "Selected Seats: " + selectedSeats.length;
        }
    </script>

<style>
        .seat {
            width: 50px;
            height: 50px;
            margin: 5px;
            text-align: center;
            vertical-align: middle;
            line-height: 20px;
            font-size: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .booked {
            background-color: red;
            color: white;
        }
        .available {
            background-color: green;
            color: white;
        }
        .selected {
            background-color: blue;
            color: white;
        }
        .seat-grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
}

.seat-row {
    display: flex;
    justify-content: center;
}

    </style>

</head>
<body>
<div class="container">
    <h2>Select a Seat at <?= htmlspecialchars($theater['name']); ?></h2>
    <p id="seat_price">Total Price: ₹0.00</p>
    <p id="seat_count">Selected Seats: 0</p>
    <div class="screen">SCREEN</div>
    <form action="user_book_ticket.php" method="POST">
        <div class="seat-grid">
            <?php
            for ($row = 0; $row < $theater['total_rows']; $row++) {
                $rowLetter = chr(65 + $row);
                echo "<div class='seat-row'>";
                for ($col = 1; $col <= $theater['total_columns']; $col++) {
                    $seatNumber = $rowLetter . $col;
                    $isBooked = isset($seats[$seatNumber]) ? $seats[$seatNumber]['is_booked'] : false;
                    $price = isset($seats[$seatNumber]) ? $seats[$seatNumber]['price'] : '0.00';
                    $disabled = $isBooked ? "disabled" : "";
                    $class = $isBooked ? "booked" : "available";
                    echo "<button type='button' id='$seatNumber' class='seat $class' $disabled onclick='selectSeat(\"$seatNumber\", \"$price\")'>$seatNumber<br><span>₹$price</span></button>";
                }
                echo "</div>";
            }
            ?>
        </div>
        <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">
        <input type="hidden" name="showtime_id" value="<?= $showtime_id; ?>">
        <input type="hidden" name="selected_seat" id="selected_seat">
        <button type="submit">Book Seat</button>
    </form>
    <a href="user_showtimes.php" class="btn">Back</a></div>
</body>
</html>
