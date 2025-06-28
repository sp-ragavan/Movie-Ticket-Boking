<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header("Location: manage_theaters.php");
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM theaters WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_theaters.php");
} else {
    echo "Error deleting theater!";
}
?>
