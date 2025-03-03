<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/activity.php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Get the trip_id from the URL
if (!isset($_GET['trip_id'])) {
    header('Location: ../create_trip.php');
    exit;
} else {
    $trip_id = $_GET['trip_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $venue = $_POST['venue'];
    $address = $_POST['address'];
    $website = $_POST['website'];
    $email = $_POST['email'];
    $cost = $_POST['cost'];

    // Insert activity details into the database
    $stmt = $pdo->prepare("INSERT INTO activity (trip_id, start_date, end_date, venue, address, website, email, cost, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$trip_id, $start_date, $end_date, $venue, $address, $website, $email, $cost]);

    header("Location: ../create_trip.php?trip_id=$trip_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Activity</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="venue" class="form-label">Venue</label>
                <input type="text" id="venue" name="venue" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" id="website" name="website" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" step="0.01" id="cost" name="cost" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Activity</button>
                <a href="../create_trip.php?trip_id=<?= $trip_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>