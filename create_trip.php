<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $trip_name = $_POST['trip_name'];
    $destination = $_POST['destination'];
    $hotel = $_POST['hotel'];
    $flight_cost = floatval($_POST['flight_cost']);
    $adults_num = intval($_POST['adults_num']);
    $childs_num = intval($_POST['childs_num']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Calculate number of nights
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $number_of_nights = max($start_date_obj->diff($end_date_obj)->days, 1);

    // Retrieve hotel price from the selected hotel
    $stmt = $pdo->prepare("SELECT price FROM hotels WHERE name = ?");
    $stmt->execute([$hotel]);
    $selected_hotel_price = $stmt->fetchColumn();

    // Calculate estimated cost
    $estimated_cost = ($flight_cost * 2) +
        ($selected_hotel_price * $number_of_nights) +
        ($childs_num * ($selected_hotel_price * 0.80) * $number_of_nights);

    // Insert trip with estimated cost
    $stmt = $pdo->prepare("INSERT INTO trips (user_id, trip_name, destination, hotel, flight_cost, adults_num, childs_num, start_date, end_date, estimated_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $trip_name, $destination, $hotel, $flight_cost, $adults_num, $childs_num, $start_date, $end_date, $estimated_cost]);

    // Get the inserted trip ID
    $trip_id = $pdo->lastInsertId();

    // Insert one flight into the flights table
    $stmt = $pdo->prepare("INSERT INTO flights (trip_id, flight, location_id, cost, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$trip_id, 'Outbound', $destination, $flight_cost, $start_date, $end_date]);

    header('Location: index.php');
    exit;
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
    <title>Create Trip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .carousel-item img {
            max-height: 500px;
            width: auto;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Create Trip</h1>
        <form method="POST" class="bg-light p-4 rounded shadow-sm" style="max-width: 800px; margin: auto;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="trip_name" class="form-label">Trip Name</label>
                    <input type="text" id="trip_name" name="trip_name" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="destination" class="form-label">Destination</label>
                    <select id="destination" name="destination" class="form-select form-select-sm" required>
                        <?php foreach ($destinations as $location_id => $location): ?>
                            <option value="<?= $location_id ?>"> <?= htmlspecialchars($location['name']) ?> </option>
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

                <input type="hidden" id="hotel" name="hotel">

                <div class="col-md-6">
                    <label for="flight_cost" class="form-label">Total 2 way Flight Cost (In dollars)</label>
                    <input type="text" id="flight_cost" name="flight_cost" class="form-control form-control-sm"
                        required>
                </div>
                <div class="col-md-3">
                    <label for="adults_num" class="form-label">No. of Adults</label>
                    <input type="text" id="adults_num" name="adults_num" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label for="childs_num" class="form-label">No. of Children</label>
                    <input type="text" id="childs_num" name="childs_num" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" required>
                </div>
            </div>

            <!-- Estimated Cost Display -->
            <div class="alert alert-info fs-5 fw-bold text-center mt-3" id="estimated-cost-display">Estimated Cost:
                $0.00</div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary btn-sm">Create Trip</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const destinationSelect = document.getElementById('destination');
        const hotelCardsContainer = document.getElementById('hotel-cards');
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

        destinationSelect.addEventListener('change', function() {
            loadHotelsForDestination(this.value);
        });

        document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', updateSelectedHotel);

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

        // Attach event listeners to update the cost dynamically
        ['flight_cost', 'adults_num', 'childs_num', 'start_date', 'end_date'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateEstimatedCost);
        });

        document.getElementById('hotel-carousel').addEventListener('slid.bs.carousel', calculateEstimatedCost);

        // Initial calculation on page load
        calculateEstimatedCost();

        // Load the initial hotels for the current destination
        loadHotelsForDestination(destinationSelect.value);
    </script>
</body>

</html>