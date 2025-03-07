<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step2'])) {
    $user_id = $_SESSION['user_id'];
    $hotel = $_POST['hotel'];
    $trip_id = $_POST['trip_id'];

    // Update the hotel for the trip
    $stmt = $pdo->prepare("UPDATE trips SET hotel = ? WHERE trip_id = ? AND user_id = ?");
    if ($stmt->execute([$hotel, $trip_id, $user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update hotel']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
