<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step1'])) {
    $user_id = $_SESSION['user_id'];
    $destination = $_POST['destination'];
    $trip_id = $_POST['trip_id'];

    // Insert trip with only the destination
    $stmt = $pdo->prepare("INSERT INTO trips (trip_id, user_id, destination) VALUES (?, ?, ?)");
    if ($stmt->execute([$trip_id, $user_id, $destination])) {
        echo json_encode(['success' => true, 'trip_id' => $trip_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create trip']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
