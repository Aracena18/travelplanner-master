<?php
// filepath: /c:/xampp/htdocs/travelplanner-master/api/sub_plans.php
include 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// Fetch activities for the current trip
$activities = [];
$stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activities[] = $row;
}

// Fetch car rentals for the current trip
$car_rentals = [];
$stmt = $pdo->prepare("SELECT * FROM car_rental WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $car_rentals[] = $row;
}

// Fetch concerts for the current trip
$concerts = [];
$stmt = $pdo->prepare("SELECT * FROM concert WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $concerts[] = $row;
}

// Fetch flights for the current trip
$flights = [];
$stmt = $pdo->prepare("SELECT * FROM flights WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flights[] = $row;
}

// Fetch meetings for the current trip
$meetings = [];
$stmt = $pdo->prepare("SELECT * FROM meeting WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $meetings[] = $row;
}

// Fetch restaurants for the current trip
$restaurants = [];
$stmt = $pdo->prepare("SELECT * FROM restaurant WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $restaurants[] = $row;
}

// Fetch transportation for the current trip
$transportations = [];
$stmt = $pdo->prepare("SELECT * FROM transportation WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transportations[] = $row;
}
?>

<!-- Display Sub Plans -->
<div class="text-center mt-4">
    <h2 class="mb-4">Sub Plans</h2>

    <?php if (!empty($activities)): ?>
        <h3>Activities</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Venue</th>
                    <th>Address</th>
                    <th>Website</th>
                    <th>Email</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?= htmlspecialchars($activity['start_date']) ?></td>
                        <td><?= htmlspecialchars($activity['end_date']) ?></td>
                        <td><?= htmlspecialchars($activity['venue']) ?></td>
                        <td><?= htmlspecialchars($activity['address']) ?></td>
                        <td><?= htmlspecialchars($activity['website']) ?></td>
                        <td><?= htmlspecialchars($activity['email']) ?></td>
                        <td>$<?= htmlspecialchars($activity['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($car_rentals)): ?>
        <h3>Car Rentals</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Car Type</th>
                    <th>Pickup Location</th>
                    <th>Dropoff Location</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($car_rentals as $car_rental): ?>
                    <tr>
                        <td><?= htmlspecialchars($car_rental['start_date']) ?></td>
                        <td><?= htmlspecialchars($car_rental['end_date']) ?></td>
                        <td><?= htmlspecialchars($car_rental['car_type']) ?></td>
                        <td><?= htmlspecialchars($car_rental['pickup_location']) ?></td>
                        <td><?= htmlspecialchars($car_rental['dropoff_location']) ?></td>
                        <td>$<?= htmlspecialchars($car_rental['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($concerts)): ?>
        <h3>Concerts</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Address</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($concerts as $concert): ?>
                    <tr>
                        <td><?= htmlspecialchars($concert['date']) ?></td>
                        <td><?= htmlspecialchars($concert['venue']) ?></td>
                        <td><?= htmlspecialchars($concert['address']) ?></td>
                        <td>$<?= htmlspecialchars($concert['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($flights)): ?>
        <h3>Flights</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Departure Date</th>
                    <th>Airline</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flights as $flight): ?>
                    <tr>
                        <td><?= htmlspecialchars($flight['departure']) ?></td>
                        <td><?= htmlspecialchars($flight['airline']) ?></td>
                        <td>$<?= htmlspecialchars($flight['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($meetings)): ?>
        <h3>Meetings</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Address</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetings as $meeting): ?>
                    <tr>
                        <td><?= htmlspecialchars($meeting['date']) ?></td>
                        <td><?= htmlspecialchars($meeting['venue']) ?></td>
                        <td><?= htmlspecialchars($meeting['address']) ?></td>
                        <td>$<?= htmlspecialchars($meeting['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($restaurants)): ?>
        <h3>Restaurants</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Restaurant</th>
                    <th>Address</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                    <tr>
                        <td><?= htmlspecialchars($restaurant['date']) ?></td>
                        <td><?= htmlspecialchars($restaurant['name']) ?></td>
                        <td><?= htmlspecialchars($restaurant['address']) ?></td>
                        <td>$<?= htmlspecialchars($restaurant['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($transportations)): ?>
        <h3>Transportations</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transportations as $transportation): ?>
                    <tr>
                        <td><?= htmlspecialchars($transportation['date']) ?></td>
                        <td><?= htmlspecialchars($transportation['type']) ?></td>
                        <td>$<?= htmlspecialchars($transportation['cost']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>