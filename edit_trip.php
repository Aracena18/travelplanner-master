<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Trip ID not provided.');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$trip = $stmt->fetch();

if (!$trip) {
    die('Trip not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_name = $_POST['trip_name'];
    $destination = $_POST['destination'];
    $hotel = $_POST['hotel'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $flight_cost = $_POST['flight_cost'];
    $adults_num = $_POST['adults_num'];
    $childs_num = $_POST['childs_num'];
    $estimated_cost = $_POST['estimated_cost'];

    $stmt = $pdo->prepare("UPDATE trips SET trip_name = ?, destination = ?, hotel = ?, start_date = ?, end_date = ?, flight_cost = ?, adults_num = ?, childs_num = ?, estimated_cost = ? WHERE id = ?");
    $stmt->execute([$trip_name, $destination, $hotel, $start_date, $end_date, $flight_cost, $adults_num, $childs_num, $estimated_cost, $id]);
    header('Location: index.php');
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .card-img-top {
            max-height: 500px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Trip</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" id="trip-form"
            style="max-width: 800px; margin: auto;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="trip_name" class="form-label">Trip Name</label>
                    <input type="text" id="trip_name" name="trip_name" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($trip['trip_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="destination" class="form-label">Destination</label>
                    <select id="destination" name="destination" class="form-select form-select-sm" required>
                        <?php foreach ($destinations as $location_id => $location): ?>
                            <option value="<?= $location_id ?>"
                                <?= $trip['destination'] === $location_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($location['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="hotel-carousel" class="carousel slide mb-3" data-bs-ride="false">
                    <div class="carousel-inner" id="hotel-cards"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#hotel-carousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hotel-carousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <div id="map" style="height: 400px;" class="mb-4"></div>

                <input type="hidden" id="hotel" name="hotel" value="<?= htmlspecialchars($trip['hotel']) ?>">
                <input type="hidden" id="estimated_cost" name="estimated_cost" value="">

                <div class="col-md-6">
                    <label for="flight_cost" class="form-label">Total 2 way Flight Cost (In dollars)</label>
                    <input type="number" id="flight_cost" name="flight_cost" min="0" step="0.01"
                        value="<?= htmlspecialchars($trip['flight_cost']) ?>" class="form-control form-control-sm"
                        required>
                </div>

                <div class="col-md-3">
                    <label for="adults_num" class="form-label">Adults Number</label>
                    <input type="number" id="adults_num" name="adults_num" min="0"
                        value="<?= htmlspecialchars($trip['adults_num']) ?>" class="form-control form-control-sm"
                        required>
                </div>

                <div class="col-md-3">
                    <label for="childs_num" class="form-label">Children Number</label>
                    <input type="number" id="childs_num" name="childs_num" min="0"
                        value="<?= htmlspecialchars($trip['childs_num']) ?>" class="form-control form-control-sm"
                        required>
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control form-control-sm"
                        value="<?= $trip['start_date'] ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control form-control-sm"
                        value="<?= $trip['end_date'] ?>" required>
                </div>
            </div>

            <div class="mb-3 text-center">
                <h4 id="estimated-cost-display" class="text-primary"></h4>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const destinationSelect = document.getElementById('destination');
        const hotelCardsContainer = document.getElementById('hotel-cards');
        const estimatedCostInput = document.getElementById('estimated_cost');
        const hotels = <?= json_encode($destinations); ?>;

        function loadHotelsForDestination(destination) {
            hotelCardsContainer.innerHTML = '';

            if (hotels[destination]) {
                hotels[destination].hotels.forEach((hotel, index) => {
                    const isActive = index === 0 ? 'active' : '';
                    const card = `
                        <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
                            <div class="card">
                                <img src="assets/images/${hotel.image_name}" class="card-img-top" alt="${hotel.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${hotel.name}</h5>
                                    <div class="d-flex justify-content-start">
                                        ${'★'.repeat(hotel.stars)}${'☆'.repeat(5 - hotel.stars)}
                                    </div>
                                    <div class="mt-3">
                                        <h5 class="text-success fs-3 fw-bold">$${hotel.price}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    hotelCardsContainer.innerHTML += card;
                });
                updateSelectedHotel();
            }
        }

        function updateSelectedHotel() {
            const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
            if (activeHotelItem) {
                const selectedHotel = activeHotelItem.getAttribute('data-hotel');
                const latitude = activeHotelItem.getAttribute('data-latitude');
                const longitude = activeHotelItem.getAttribute('data-longitude');
                document.getElementById('hotel').value = selectedHotel;
                updateMapMarker(latitude, longitude);
            }
        }

        function calculateEstimatedCost() {
            const flightCost = parseFloat(document.getElementById('flight_cost').value) || 0;
            const adultsNum = parseInt(document.getElementById('adults_num').value) || 0;
            const childsNum = parseInt(document.getElementById('childs_num').value) || 0;
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
            const hotelCost = activeHotelItem ? parseFloat(activeHotelItem.querySelector('.text-success').textContent
                .replace('$', '')) : 0;

            const numberOfNights = Math.max((endDate - startDate) / (1000 * 60 * 60 * 24), 1);

            // Calculate the number of rooms needed
            const totalPeople = adultsNum + childsNum;
            const roomsNeeded = Math.ceil(totalPeople / 2);

            // Calculate the estimated cost
            const estimatedCost = (flightCost * adultsNum) +
                (hotelCost * roomsNeeded * numberOfNights);

            document.getElementById('estimated-cost-display').textContent = `Estimated Cost: $${estimatedCost.toFixed(2)}`;
        }


        destinationSelect.addEventListener('change', function() {
            loadHotelsForDestination(this.value);
            calculateEstimatedCost();
        });

        document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', function() {
            updateSelectedHotel();
            calculateEstimatedCost();
        });

        ['flight_cost', 'adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateEstimatedCost);
        });

        const map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let currentMarker;

        function updateMapMarker(lat, lng) {
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            if (lat && lng) {
                currentMarker = L.marker([lat, lng]).addTo(map)
                    .bindPopup("Hotel Location").openPopup();
                map.setView([lat, lng], 10);
            }
        }

        loadHotelsForDestination(destinationSelect.value);
        calculateEstimatedCost();
    </script>
</body>

</html>