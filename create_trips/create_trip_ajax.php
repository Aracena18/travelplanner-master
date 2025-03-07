<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_trip'])) {
    $user_id = $_SESSION['user_id'];
    $destination = $_POST['destination'];

    try {
        // Check if a trip already exists for this user and destination
        $stmt = $pdo->prepare("SELECT trip_id FROM trips WHERE user_id = ? AND destination = ?");
        $stmt->execute([$user_id, $destination]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trip) {
            // Insert a new trip
            $stmt = $pdo->prepare("INSERT INTO trips (user_id, destination) VALUES (?, ?)");
            $stmt->execute([$user_id, $destination]);
            $trip_id = $pdo->lastInsertId();
        } else {
            $trip_id = $trip['trip_id'];
        }

        echo json_encode(["success" => true, "trip_id" => $trip_id]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
