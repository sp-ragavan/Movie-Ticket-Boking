<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header("Location: manage_canteen.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM canteen_menu WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Canteen Item</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="dashboard">
        <h2>Edit Canteen Item</h2>
        <form method="POST" action="update_canteen.php">
            <input type="hidden" name="id" value="<?= $item['id']; ?>">
            <label>Item Name:</label>
            <input type="text" name="item_name" value="<?= $item['item_name']; ?>" required>
            <label>Price (₹):</label>
            <input type="number" name="price" value="<?= $item['price']; ?>" step="0.01" required>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>

