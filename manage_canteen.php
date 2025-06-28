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

// Fetch canteen items based on selected theater
$canteenItemsResult = null;
if (isset($_POST['theater_id'])) {
    $theater_id = $_POST['theater_id'];
    $canteenQuery = "SELECT * FROM canteen_menu WHERE theater_id = ?";
    $stmt = $conn->prepare($canteenQuery);
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $canteenItemsResult = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Canteen Items</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="menu">
        <h2>Manage Canteen Items</h2>

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

        <?php if ($canteenItemsResult) { ?>
        <h3>Canteen Menu</h3>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Price (₹)</th>
                <th>Actions</th>
            </tr>
            <?php while ($item = $canteenItemsResult->fetch_assoc()) { ?>
            <tr>
                <td><?= $item['item_name']; ?></td>
                <td><?= number_format($item['price'], 2); ?></td>
                <td>
                    <a href="edit_canteen.php?id=<?= $item['id']; ?>">Edit</a> |
                    <a href="delete_canteen.php?id=<?= $item['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h3>Add New Item</h3>
        <form method="POST" action="add_canteen.php">
            <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">
            <input type="text" name="item_name" required placeholder="Item Name">
            <input type="text" name="price" required placeholder="Price">
            <button type="submit">Add Item</button>
        </form>
        <?php } ?>
    </div>
    <a href="admin_dashboard.php" class="menuback">Back to Dashboard</a>
</body>
</html>
