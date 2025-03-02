<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/flights.php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $departure_date = $_POST['departure_date'];
    $airline = $_POST['airline'];
    $flight_cost = $_POST['flight_cost'];

    // Insert flight details into the database
    $stmt = $pdo->prepare("INSERT INTO flights (user_id, departure_date, airline, flight_cost) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $departure_date, $airline, $flight_cost]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Flight</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="departure_date" class="form-label">Departure Date</label>
                <input type="date" id="departure_date" name="departure_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="airline" class="form-label">Airline</label>
                <input type="text" id="airline" name="airline" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="flight_cost" class="form-label">Flight Cost</label>
                <input type="number" step="0.01" id="flight_cost" name="flight_cost" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Flight</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>