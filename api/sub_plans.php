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

// Initialize total cost
$total_cost = 0;

// Fetch activities for the current trip
$activities = [];
$stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activities[] = $row;
    $total_cost += $row['cost'];
}

// Fetch car rentals for the current trip
$car_rentals = [];
$stmt = $pdo->prepare("SELECT * FROM car_rental WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $car_rentals[] = $row;
    $total_cost += $row['cost'];
}

// Fetch concerts for the current trip
$concerts = [];
$stmt = $pdo->prepare("SELECT * FROM concert WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $concerts[] = $row;
    $total_cost += $row['cost'];
}

// Fetch flights for the current trip
$flights = [];
$stmt = $pdo->prepare("SELECT * FROM flights WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $flights[] = $row;
    $total_cost += $row['cost'];
}

// Fetch meetings for the current trip
$meetings = [];
$stmt = $pdo->prepare("SELECT * FROM meeting WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $meetings[] = $row;
    $total_cost += $row['cost'];
}

// Fetch restaurants for the current trip
$restaurants = [];
$stmt = $pdo->prepare("SELECT * FROM restaurant WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $restaurants[] = $row;
    $total_cost += $row['price'];
}

// Fetch transportation for the current trip
$transportations = [];
$stmt = $pdo->prepare("SELECT * FROM transportation WHERE trip_id = ? ORDER BY created_at");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transportations[] = $row;
    $total_cost += $row['transportation_cost'];
}
?>

<!-- Display Sub Plans -->
<div class="text-center mt-4">
    <h2 class="mb-4">Sub Plans</h2>

    <?php if (!empty($activities)): ?>
        <h3>Activities</h3>
        <div class="row justify-content-start">
            <?php foreach ($activities as $activity): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($activity['venue']) ?></h5>
                            <p class="card-text">
                                <strong>Start Date:</strong> <?= htmlspecialchars($activity['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($activity['end_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($activity['start_time']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($activity['end_time']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($activity['address']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($activity['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($activity['email']) ?><br>
                                <strong>Cost:</strong> $<?= htmlspecialchars($activity['cost']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($car_rentals)): ?>
        <h3>Car Rentals</h3>
        <div class="row justify-content-start">
            <?php foreach ($car_rentals as $car_rental): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($car_rental['rental_agency']) ?></h5>
                            <p class="card-text">
                                <strong>Start Date:</strong> <?= htmlspecialchars($car_rental['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($car_rental['end_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($car_rental['start_time']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($car_rental['end_time']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($car_rental['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($car_rental['email']) ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($car_rental['phone']) ?><br>
                                <strong>Cost:</strong> $<?= htmlspecialchars($car_rental['cost']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($concerts)): ?>
        <h3>Concerts</h3>
        <div class="row justify-content-start">
            <?php foreach ($concerts as $concert): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($concert['event_name']) ?></h5>
                            <p class="card-text">
                                <strong>Start Date:</strong> <?= htmlspecialchars($concert['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($concert['end_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($concert['start_time']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($concert['end_time']) ?><br>
                                <strong>Venue:</strong> <?= htmlspecialchars($concert['venue']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($concert['address']) ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($concert['phone']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($concert['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($concert['email']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($flights)): ?>
        <h3>Flights</h3>
        <div class="row justify-content-start">
            <?php foreach ($flights as $flight): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($flight['airline']) ?></h5>
                            <p class="card-text">
                                <strong>Flight:</strong> <?= htmlspecialchars($flight['flight']) ?><br>
                                <strong>Location ID:</strong> <?= htmlspecialchars($flight['location_id']) ?><br>
                                <strong>Cost:</strong> $<?= htmlspecialchars($flight['cost']) ?><br>
                                <strong>Departure Date:</strong> <?= htmlspecialchars($flight['departure_date']) ?><br>
                                <strong>Start Date:</strong> <?= htmlspecialchars($flight['start_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($flight['start_time']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($flight['end_date']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($flight['end_time']) ?><br>
                                <strong>Departure Time:</strong> <?= htmlspecialchars($flight['departure_time']) ?><br>
                                <strong>Arrival Date:</strong> <?= htmlspecialchars($flight['arrival_date']) ?><br>
                                <strong>Arrival Time:</strong> <?= htmlspecialchars($flight['arrival_time']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($meetings)): ?>
        <h3>Meetings</h3>
        <div class="row justify-content-start">
            <?php foreach ($meetings as $meeting): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($meeting['event_name']) ?></h5>
                            <p class="card-text">
                                <strong>Start Date:</strong> <?= htmlspecialchars($meeting['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($meeting['end_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($meeting['start_time']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($meeting['end_time']) ?><br>
                                <strong>Venue:</strong> <?= htmlspecialchars($meeting['venue']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($meeting['address']) ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($meeting['phone']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($meeting['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($meeting['email']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($restaurants)): ?>
        <h3>Restaurants</h3>
        <div class="row justify-content-start">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($restaurant['cuisine']) ?></h5>
                            <p class="card-text">
                                <strong>Start Date:</strong> <?= htmlspecialchars($restaurant['start_date']) ?><br>
                                <strong>End Date:</strong> <?= htmlspecialchars($restaurant['end_date']) ?><br>
                                <strong>Start Time:</strong> <?= htmlspecialchars($restaurant['start_time']) ?><br>
                                <strong>End Time:</strong> <?= htmlspecialchars($restaurant['end_time']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($restaurant['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?><br>
                                <strong>Party Size:</strong> <?= htmlspecialchars($restaurant['party_size']) ?><br>
                                <strong>Dress Code:</strong> <?= htmlspecialchars($restaurant['dress_code']) ?><br>
                                <strong>Price:</strong> $<?= htmlspecialchars($restaurant['price']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($transportations)): ?>
        <h3>Transportations</h3>
        <div class="row justify-content-start">
            <?php foreach ($transportations as $transportation): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-start">
                            <h5 class="card-title"><?= htmlspecialchars($transportation['vehicle_info']) ?></h5>
                            <p class="card-text">
                                <strong>Departure Date:</strong> <?= htmlspecialchars($transportation['departure_date']) ?><br>
                                <strong>Departure Time:</strong> <?= htmlspecialchars($transportation['departure_time']) ?><br>
                                <strong>Arrival Date:</strong> <?= htmlspecialchars($transportation['arrival_date']) ?><br>
                                <strong>Arrival Time:</strong> <?= htmlspecialchars($transportation['arrival_time']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($transportation['address']) ?><br>
                                <strong>Location Name:</strong> <?= htmlspecialchars($transportation['location_name']) ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($transportation['phone']) ?><br>
                                <strong>Website:</strong> <?= htmlspecialchars($transportation['website']) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($transportation['email']) ?><br>
                                <strong>Vehicle Description:</strong>
                                <?= htmlspecialchars($transportation['vehicle_description']) ?><br>
                                <strong>Number of Passengers:</strong>
                                <?= htmlspecialchars($transportation['number_of_passengers']) ?><br>
                                <strong>Transportation Cost:</strong>
                                $<?= htmlspecialchars($transportation['transportation_cost']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>