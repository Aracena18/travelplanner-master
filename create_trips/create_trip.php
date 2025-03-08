<?php 
include 'db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely retrieve POST variables using null coalescing to avoid undefined array key warnings
    $user_id     = $_SESSION['user_id'];
    $trip_name   = $_POST['trip_name'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $hotel       = $_POST['hotel'] ?? '';
    $adults_num  = isset($_POST['adults_num']) ? intval($_POST['adults_num']) : 0;
    $childs_num  = isset($_POST['childs_num']) ? intval($_POST['childs_num']) : 0;
    $start_date  = $_POST['start_date'] ?? '';
    $end_date    = $_POST['end_date'] ?? '';

    // If trip_name is empty, fetch the destination name from the DB
    if (empty($trip_name)) {
        $stmt = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
        $stmt->execute([$destination]);
        $trip_name = $stmt->fetchColumn();
    }

    // Prefix "Trip to " plus the destination name
    $trip_name = "Trip to " . trim($trip_name);

    // Calculate the number of nights (at least 1)
    $start_date_obj = new DateTime($start_date);
    $end_date_obj   = new DateTime($end_date);
    $number_of_nights = max($start_date_obj->diff($end_date_obj)->days, 1);

    // Retrieve hotel price from the selected hotel
    $stmt = $pdo->prepare("SELECT price FROM hotels WHERE name = ?");
    $stmt->execute([$hotel]);
    $selected_hotel_price = $stmt->fetchColumn();

    // Calculate estimated cost
    $estimated_cost = ($selected_hotel_price * $number_of_nights)
                    + ($childs_num * ($selected_hotel_price * 0.80) * $number_of_nights);

    // ***** Insert WITHOUT specifying trip_id *****
    // Let MySQL auto-increment the primary key.
    $stmt = $pdo->prepare("
        INSERT INTO trips (
            user_id, trip_name, destination, hotel,
            adults_num, childs_num, start_date, end_date, estimated_cost
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $trip_name,
        $destination,
        $hotel,
        $adults_num,
        $childs_num,
        $start_date,
        $end_date,
        $estimated_cost
    ]);

    // Get the auto-generated trip_id
    $newTripId = $pdo->lastInsertId();

    // Redirect to edit_trip.php with the newly generated trip_id
    header("Location: /travelplanner-master/edit_trip.php?trip_id={$newTripId}");
    exit;
}

// GET request: Display the create trip form

// Fetch destinations and hotels
$destinations = [];
$stmt = $pdo->query("
    SELECT DISTINCT h.location_id, l.name as location_name
    FROM hotels h
    JOIN locations l ON h.location_id = l.id
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $location_id   = $row['location_id'];
    $location_name = $row['location_name'];
    $destinations[$location_id] = ['name' => $location_name, 'hotels' => []];

    $hotel_stmt = $pdo->prepare("SELECT * FROM hotels WHERE location_id = ?");
    $hotel_stmt->execute([$location_id]);
    while ($hotel_row = $hotel_stmt->fetch(PDO::FETCH_ASSOC)) {
        $destinations[$location_id]['hotels'][] = $hotel_row;
    }
}

// You may or may not need a "trip_id" on GET if you're strictly creating a new trip.
// Remove any random trip_id generation, because we rely on auto-increment now.

// Fetch activities for the current trip (if needed, or skip if this is strictly "create")
$activities = [];
if (isset($_GET['trip_id'])) {
    $trip_id = $_GET['trip_id'];
    $stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ?");
    $stmt->execute([$trip_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $activities[] = $row;
    }
}

// Include the HTML template
include 'create_trip_template.php';
?>
