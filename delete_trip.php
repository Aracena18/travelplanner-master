<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Change 'user_id' to 'id' to match the URL parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID");
}

$id = $_GET['id'];  // Now using 'id' instead of 'user_id'

// Verify the trip exists before deleting
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$trip = $stmt->fetch();

if (!$trip) {
    die("No matching record found.");
}

// Delete the trip
$stmt = $pdo->prepare("DELETE FROM trips WHERE id = ? AND user_id = ?");
$result = $stmt->execute([$id, $_SESSION['user_id']]);

if (!$result) {
    die("Failed to delete record.");
}

// Redirect to index
header('Location: index.php');
exit;
