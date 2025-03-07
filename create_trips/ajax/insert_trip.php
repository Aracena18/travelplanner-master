<?php
require 'db_connection.php'; // Ensure you have a database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $destination_id = $_POST['destination_id'] ?? null;

    if (!$destination_id) {
        echo json_encode(["success" => false, "error" => "No destination selected"]);
        exit;
    }

    // Insert new trip into the database
    $query = "INSERT INTO trips (trip_id, destination_id, created_at) VALUES (UUID(), ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $destination_id);

    if ($stmt->execute()) {
        $trip_id = $conn->insert_id; // Fetch the generated trip_id
        echo json_encode(["success" => true, "trip_id" => $trip_id]);
    } else {
        echo json_encode(["success" => false, "error" => "Database insertion failed"]);
    }

    $stmt->close();
    $conn->close();
}
