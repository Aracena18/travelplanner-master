<?php
session_start();
ob_start();

require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$searchQuery = $_GET['query'] ?? '';

try {
    // Determine if a search query is present
    if (!empty($searchQuery)) {
        $stmt = $pdo->prepare(
            "SELECT t.*, l.name as location_name FROM trips t
            JOIN locations l ON t.destination = l.id
            WHERE t.user_id = ? 
            AND (t.trip_name LIKE ? OR l.name LIKE ? OR t.hotel LIKE ?) 
            ORDER BY t.start_date ASC"
        );
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->execute([$user_id, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT t.*, l.name as location_name FROM trips t
            JOIN locations l ON t.destination = l.id
            WHERE t.user_id = ? AND t.end_date >= ? 
            ORDER BY t.start_date ASC"
        );
        $stmt->execute([$user_id, date('Y-m-d')]);
    }

    $upcomingTrips = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching trips: " . $e->getMessage());
}

// Fetch destinations and hotels from the database
$destinations = [];
$stmt = $pdo->query("SELECT DISTINCT h.location_id, l.name as location_name FROM hotels h JOIN locations l ON h.location_id = l.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $location_id = $row['location_id'];
    $location_name = $row['location_name'];
    $destinations[$location_id] = ['name' => $location_name, 'hotels' => []];

    $hotel_stmt = $pdo->prepare("SELECT * FROM hotels WHERE location_id = ?");
    $hotel_stmt->execute([$location_id]);
    while ($hotel_row = $hotel_stmt->fetch(PDO::FETCH_ASSOC)) {
        $destinations[$location_id]['hotels'][] = $hotel_row;
    }
}

function getHotelImage($destination, $hotelName)
{
    global $destinations;
    if (isset($destinations[$destination])) {
        foreach ($destinations[$destination]['hotels'] as $hotel) {
            if ($hotel['name'] === $hotelName) {
                return "assets/images/" . $hotel['image_name'];
            }
        }
    }
    return "assets/images/hotel.png";
}

ob_end_flush();
?>

<div class="container mt-5">
    <h1><?= !empty($searchQuery) ? "Search Results for: \"" . htmlspecialchars($searchQuery) . "\"" : "Upcoming Trips" ?>
    </h1>

    <div class="container mt-4 text-center">
        <form id="create-trip-form" method="POST" action="create_trip.php">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-plus-circle me-2"></i>Add New Trip
            </button>
        </form>
    </div>

    <?php if (count($upcomingTrips) > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($upcomingTrips as $trip): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <!-- Hotel Image -->
                        <img src="<?= htmlspecialchars(getHotelImage($trip['destination'], $trip['hotel'])) ?>"
                            alt="Hotel Image" class="card-img-top"
                            style="height: 150px; object-fit: cover; border-radius: 8px;">

                        <!-- Trip Details -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                            <p class="card-text">
                                <strong>Destination:</strong> <?= htmlspecialchars($trip['location_name']) ?><br>
                                <strong>Hotel:</strong> <?= htmlspecialchars($trip['hotel']) ?><br>
                                <strong>Adults:</strong> <?= htmlspecialchars($trip['adults_num']) ?><br>
                                <strong>Children:</strong> <?= htmlspecialchars($trip['childs_num']) ?><br>
                                <strong>Start Date:</strong> <?= htmlspecialchars($trip['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($trip['end_date']) ?><br>
                                <strong>Estimated Cost:</strong> <?= htmlspecialchars('$' . $trip['estimated_cost']) ?>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-footer d-flex justify-content-between">
                            <a href="edit_trip.php?trip_id=<?= $trip['trip_id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_trip.php?trip_id=<?= $trip['trip_id'] ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this trip?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="mt-3"><?= $searchQuery ? "No trips match your search query." : "No upcoming trips found." ?></p>
    <?php endif; ?>

</div>
</body>

</html>