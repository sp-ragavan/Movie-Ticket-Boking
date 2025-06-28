<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header("Location: manage_canteen.php");
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM canteen_menu WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_canteen.php");
?>
