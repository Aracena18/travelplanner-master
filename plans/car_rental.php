<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/car_rental.php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $trip_id = $_GET['trip_id'];
    $cost = $_POST['cost'];
    $rental_agency = $_POST['rental_agency'];
    $website = $_POST['website'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Insert car rental details into the database
    $stmt = $pdo->prepare("INSERT INTO car_rental (user_id, trip_id, cost, rental_agency, website, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $trip_id, $cost, $rental_agency, $website, $email, $phone]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Car Rental</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" step="0.01" id="cost" name="cost" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="rental_agency" class="form-label">Rental Agency</label>
                <input type="text" id="rental_agency" name="rental_agency" class="form-control" required>
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
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Car Rental</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>