<?php
include 'db.php';
include 'destinations.php'; // Include destinations and hotel data
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
    $companions = $_POST['companions'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $pdo->prepare("UPDATE trips SET trip_name = ?, destination = ?, hotel = ?, companions = ?, start_date = ?, end_date = ? WHERE id = ?");
    $stmt->execute([$trip_name, $destination, $hotel, $companions, $start_date, $end_date, $id]);
    header('Location: index.php');
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
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="trip_name" class="form-label">Trip Name</label>
                <input type="text" id="trip_name" name="trip_name" class="form-control"
                    value="<?= htmlspecialchars($trip['trip_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="destination" class="form-label">Destination</label>
                <select id="destination" name="destination" class="form-select" required>
                    <?php foreach ($destinations as $location => $hotels): ?>
                        <option value="<?= $location ?>" <?= $trip['destination'] === $location ? 'selected' : '' ?>>
                            <?= $location ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="hotel-carousel" class="carousel slide" data-bs-ride="false">
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

            <input type="hidden" id="hotel" name="hotel" value="<?= htmlspecialchars($trip['hotel']) ?>">

            <div class="mb-3">
                <label for="flight_cost" class="form-label">Flight Cost (One way) in dollars</label>
                <input type="text" id="flight_cost" name="flight_cost"
                    value="<?= htmlspecialchars($trip['flight_cost']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="adults_num" class="form-label">Adults Number</label>
                <input type="text" id="adults_num" name="adults_num"
                    value="<?= htmlspecialchars($trip['adults_num']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="childs_num" class="form-label">Children Number</label>
                <input type="text" id="childs_num" name="childs_num"
                    value="<?= htmlspecialchars($trip['childs_num']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                    value="<?= $trip['start_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $trip['end_date'] ?>"
                    required>
            </div>

            <div id="map" style="height: 400px;" class="mb-4"></div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
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
            const tripHotel = "<?= htmlspecialchars($trip['hotel']) ?>";

            if (hotels[destination]) {
                hotels[destination].forEach((hotel, index) => {
                    const isActive = tripHotel === hotel.name ? 'active' : '';
                    const card = `
                        <div class="carousel-item ${isActive}" data-hotel="${hotel.name}" data-latitude="${hotel.latitude}" data-longitude="${hotel.longitude}">
                            <div class="card">
                                <img src="assets/images/${hotel.image}" class="card-img-top" alt="${hotel.name}">
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

        // Load the initial hotels for the current destination
        loadHotelsForDestination(destinationSelect.value);
    </script>
</body>

</html>