<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['theater_id']) || !isset($_POST['item_name']) || !isset($_POST['price'])) {
    header("Location: manage_canteen.php");
    exit();
}

$theater_id = $_POST['theater_id'];
$item_name = $_POST['item_name'];
$price = $_POST['price'];

$query = "INSERT INTO canteen_menu (theater_id, item_name, price) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("isd", $theater_id, $item_name, $price);
$stmt->execute();

header("Location: manage_canteen.php");
?>
