<?php
require '../config/database.php'; // Adjust the path

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $trip_id = $_POST['trip_id'];
    $updates = [];

    if (!empty($_POST['destination_id'])) {
        $updates[] = "destination_id=" . intval($_POST['destination_id']);
    }
    if (!empty($_POST['hotel_id'])) {
        $updates[] = "hotel_id=" . intval($_POST['hotel_id']);
    }
    if (!empty($_POST['adults']) && !empty($_POST['children']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $updates[] = "adults=" . intval($_POST['adults']);
        $updates[] = "children=" . intval($_POST['children']);
        $updates[] = "start_date='" . $_POST['start_date'] . "'";
        $updates[] = "end_date='" . $_POST['end_date'] . "'";
    }

    if (!empty($updates)) {
        $query = "UPDATE trips SET " . implode(', ', $updates) . " WHERE id=?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$trip_id]);
    }

    echo json_encode(['status' => 'success']);
}
