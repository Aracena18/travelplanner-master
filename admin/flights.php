<?php
include '../db.php';
session_start();

if (!isset($_SESSION['is_admin'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Handle form submission for adding a new flight
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_flight'])) {
    $location_id = $_POST['location_id'];
    $cost = $_POST['cost'];

    $stmt = $pdo->prepare("INSERT INTO flights (location_id, cost) VALUES (?, ?)");
    $stmt->execute([$location_id, $cost]);

    header('Location: flights.php');
    exit;
}

// Handle form submission for updating a flight
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_flight'])) {
    $id = $_POST['id'];
    $location_id = $_POST['location_id'];
    $cost = $_POST['cost'];

    $stmt = $pdo->prepare("UPDATE flights SET location_id = ?, cost = ? WHERE id = ?");
    $stmt->execute([$location_id, $cost, $id]);

    header('Location: flights.php');
    exit;
}

// Handle flight deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM flights WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: flights.php');
    exit;
}

// Fetch flights from the database
$flights = [];
$stmt = $pdo->query("SELECT f.*, l.name as location_name FROM flights f JOIN locations l ON f.location_id = l.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flights[] = $row;
}

// Fetch locations for the dropdown
$locations = [];
$stmt = $pdo->query("SELECT * FROM locations");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $locations[] = $row;
}

include 'admin_layout.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 280px;
            /* Adjusted to match the sidebar width */
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Main content -->
    <div class="main-content">
        <h1 class="text-center mb-4">Flights</h1>

        <!-- Flights Table -->
        <div class="container">
            <h2>Manage Flights</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Location</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['id']) ?></td>
                            <td><?= htmlspecialchars($flight['location_name']) ?></td>
                            <td><?= htmlspecialchars($flight['cost']) ?></td>
                            <td>
                                <a href="edit_flight.php?id=<?= $flight['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="flights.php?delete=<?= $flight['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div> <!-- Close main-content div -->
</body>

</html>

<?php
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
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert restaurant details into the database
    $stmt = $pdo->prepare("INSERT INTO restaurant (user_id, address, phone, website, email, cuisine, party_size, dress_code, price, start_time, end_time, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $address, $phone, $website, $email, $cuisine, $party_size, $dress_code, $price, $start_time, $end_time]);

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
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" id="start_time" name="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" id="end_time" name="end_time" class="form-control" required>
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