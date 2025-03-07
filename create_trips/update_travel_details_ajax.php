<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step3'])) {
    $user_id = $_SESSION['user_id'];
    $trip_id = $_POST['trip_id'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Update the travel details for the trip
    $stmt = $pdo->prepare("UPDATE trips SET $field = ? WHERE trip_id = ? AND user_id = ?");
    if ($stmt->execute([$value, $trip_id, $user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update travel details']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
