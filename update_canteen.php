<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['id']) || !isset($_POST['item_name']) || !isset($_POST['price'])) {
    header("Location: manage_canteen.php");
    exit();
}

$id = $_POST['id'];
$item_name = $_POST['item_name'];
$price = $_POST['price'];

$query = "UPDATE canteen_menu SET item_name = ?, price = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sdi", $item_name, $price, $id);
$stmt->execute();

header("Location: manage_canteen.php");
?>
