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

// Fetch showtimes if theater is selected
$showtimesResult = null;
$theater_id = null;
if (isset($_POST['theater_id'])) {
    $theater_id = $_POST['theater_id'];
    $showtimesQuery = "SELECT * FROM showtimes WHERE theater_id = ?";
    $stmt = $conn->prepare($showtimesQuery);
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $showtimesResult = $stmt->get_result();

    // Fetch theater details for row/column info
    $stmt = $conn->prepare("SELECT total_rows, total_columns FROM theaters WHERE id = ?");
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $theaterConfig = $stmt->get_result()->fetch_assoc();
}

// Fetch seats if showtime is selected
$seatsResult = null;
$showtime_id = null;
if (isset($_POST['showtime_id'])) {
    $showtime_id = $_POST['showtime_id'];
    $seatsQuery = "SELECT * FROM seats WHERE showtime_id = ?";
    $stmt = $conn->prepare($seatsQuery);
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $seatsResult = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Seats</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="seat">
        <h2>Manage Seats</h2>

        <form method="POST">
            <select name="theater_id" required onchange="this.form.submit()">
                <option value="">Select Theater</option>
                <?php while ($theater = $theatersResult->fetch_assoc()) { ?>
                    <option value="<?= $theater['id']; ?>" <?= isset($theater_id) && $theater_id == $theater['id'] ? 'selected' : ''; ?>>
                        <?= $theater['name']; ?>
                    </option>
                <?php } ?>
            </select>
        </form>

        <?php if ($showtimesResult) { ?>
        <form method="POST">
            <select name="showtime_id" required onchange="this.form.submit()">
                <option value="">Select Showtime</option>
                <?php while ($showtime = $showtimesResult->fetch_assoc()) { ?>
                    <option value="<?= $showtime['id']; ?>" <?= isset($showtime_id) && $showtime_id == $showtime['id'] ? 'selected' : ''; ?>>
                        <?= $showtime['show_date'] . " - " . $showtime['show_time']; ?>
                    </option>
                <?php } ?>
            </select>
            <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">
        </form>
        <?php } ?>

        <?php if ($theater_id) { ?>
        <form method="POST" action="update_theater_seats.php">
            <label>Set Rows</label><br>
            <input type="text" name="total_rows" value="<?= $theaterConfig['total_rows']; ?>" required><br>
            <label>Set Columns</label><br>
            <input type="text" name="total_columns" value="<?= $theaterConfig['total_columns']; ?>" required>
            <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">
            <button type="submit">Update</button>
        </form>
        <?php } ?>
        </div>

        <?php if ($seatsResult && $theaterConfig) { ?>
    <h3>Seat Layout</h3>
    <div class="screen">SCREEN</div>

    <form method="POST" action="update_seat_price.php">
        <table class="seat-layout">
            <?php
            $seatData = [];
            while ($seat = $seatsResult->fetch_assoc()) {
                $seatData[$seat['seat_number']] = $seat;
            }

            for ($row = 0; $row < $theaterConfig['total_rows']; $row++) {
                $rowLetter = chr(65 + $row); // Convert to A, B, C...
                echo "<tr>";
                for ($col = 1; $col <= $theaterConfig['total_columns']; $col++) {
                    $seatNum = $rowLetter . $col;
                    $price = isset($seatData[$seatNum]) ? $seatData[$seatNum]['price'] : '0.00';
                    echo "<td>
                            <input type='hidden' name='seat_numbers[]' value='$seatNum'>
                            <input type='text' name='prices[]' value='$price' size='3'>
                        </td>";
                }
                echo "</tr>";
            }
            ?>
        </table>

        <input type="hidden" name="showtime_id" value="<?= $showtime_id; ?>">
        <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">
        <button type="submit">Update All Prices</button>
    </form>
<?php } ?>
<a href="admin_dashboard.php" class="seatback">Back to Dashboard</a>
</body>
</html>
