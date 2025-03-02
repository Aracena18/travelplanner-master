<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/plans/restaurant.php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $website = $_POST['website'];
    $email = $_POST['email'];
    $cuisine = $_POST['cuisine'];
    $party_size = $_POST['party_size'];
    $dress_code = $_POST['dress_code'];
    $price = $_POST['price'];

    // Insert restaurant details into the database
    $stmt = $pdo->prepare("INSERT INTO restaurant (address, phone, website, email, cuisine, party_size, dress_code, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$address, $phone, $website, $email, $cuisine, $party_size, $dress_code, $price]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Restaurant</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" required>
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
                <label for="cuisine" class="form-label">Cuisine</label>
                <input type="text" id="cuisine" name="cuisine" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="party_size" class="form-label">Party Size</label>
                <input type="number" id="party_size" name="party_size" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="dress_code" class="form-label">Dress Code</label>
                <input type="text" id="dress_code" name="dress_code" class="form-control">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Restaurant</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>