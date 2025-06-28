<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['theater_id'])) {
    header("Location: manage_seats.php");
    exit();
}

$theater_id = $_POST['theater_id'];
$total_rows = $_POST['total_rows'];
$total_columns = $_POST['total_columns'];

$query = "UPDATE theaters SET total_rows = ?, total_columns = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $total_rows, $total_columns, $theater_id);
$stmt->execute();

header("Location: manage_seats.php?theater_id=$theater_id");
?>
