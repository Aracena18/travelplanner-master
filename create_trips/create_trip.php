<?php 
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Generate a unique trip ID if not already present in the URL
if (!isset($_GET['trip_id'])) {
    $trip_id = random_int(100000, 999999);
    header("Location:/travelplanner-master/create_trips/create_trip.php?trip_id=$trip_id");
    exit;
} else {
    $trip_id = $_GET['trip_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    // Get the hidden trip name which now reflects the selected destination's name
    $trip_name = $_POST['trip_name'];
    $destination = $_POST['destination'];
    $hotel = $_POST['hotel'];
    $adults_num = intval($_POST['adults_num']);
    $childs_num = intval($_POST['childs_num']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Fallback: if the trip name is empty, retrieve the destination name from the database
    if (empty($trip_name)) {
        $stmt = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
        $stmt->execute([$destination]);
        $trip_name = $stmt->fetchColumn();
    }
    // Set the trip name to "Trip to" plus the destination name
    $trip_name = "Trip to " . trim($trip_name);

    // Calculate number of nights
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $number_of_nights = max($start_date_obj->diff($end_date_obj)->days, 1);

    // Retrieve hotel price from the selected hotel
    $stmt = $pdo->prepare("SELECT price FROM hotels WHERE name = ?");
    $stmt->execute([$hotel]);
    $selected_hotel_price = $stmt->fetchColumn();

    // Calculate estimated cost
    $estimated_cost = ($selected_hotel_price * $number_of_nights) +
        ($childs_num * ($selected_hotel_price * 0.80) * $number_of_nights);

    // Insert trip with estimated cost
    $stmt = $pdo->prepare("INSERT INTO trips (trip_id, user_id, trip_name, destination, hotel, adults_num, childs_num, start_date, end_date, estimated_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$trip_id, $user_id, $trip_name, $destination, $hotel, $adults_num, $childs_num, $start_date, $end_date, $estimated_cost]);

    header("Location:/travelplanner-master/edit_trip.php?trip_id=$trip_id");
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

// Fetch activities for the current trip
$activities = [];
$stmt = $pdo->prepare("SELECT * FROM activity WHERE trip_id = ?");
$stmt->execute([$trip_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activities[] = $row;
}

// Include the HTML template (the view)
include 'create_trip_template.php';
<<<<<<< HEAD
<<<<<<< HEAD
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tripId = new URLSearchParams(window.location.search).get('trip_id') || localStorage.getItem(
        'trip_id');
    localStorage.setItem('trip_id', tripId); // Store for later use

    const destinationElement = document.getElementById('destination');
    const tripNameInput = document.getElementById('trip_name');

    // Insert trip when destination is selected
    destinationElement.addEventListener('change', function() {
        const destinationId = this.value;
        const destinationName = this.options[this.selectedIndex].text;

        tripNameInput.value = destinationName; // Update hidden input

        if (tripId) {
            insertNewTrip(tripId, destinationId, destinationName);
        }
    });

    function insertNewTrip(tripId, destinationId, destinationName) {
        fetch('ajax/insert_trip.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `trip_id=${tripId}&destination_id=${destinationId}&trip_name=${encodeURIComponent(destinationName)}`
            })
            .then(response => response.json())
            .then(data => console.log('Trip inserted:', data))
            .catch(error => console.error('Error:', error));
    }

    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = parseInt(document.querySelector('.form-step.active').dataset
                .step);
            updateTripData(currentStep);
        });
    });

    function updateTripData(step) {
        let data = `trip_id=${tripId}`;
        switch (step) {
            case 1:
                data += `&destination_id=${destinationElement.value}`;
                break;
            case 2:
                const hotelId = document.getElementById('hotel').value;
                data += `&hotel_id=${hotelId}`;
                break;
            case 3:
                const adults = document.getElementById('adults_num').value;
                const children = document.getElementById('childs_num').value;
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                data += `&adults=${adults}&children=${children}&start_date=${startDate}&end_date=${endDate}`;
                break;
        }

        fetch('ajax/update_trip.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: data
            })
            .then(response => response.json())
            .then(data => console.log('Trip updated:', data))
            .catch(error => console.error('Error:', error));
    }
});
</script>
=======
>>>>>>> parent of a7ff2b5 (it no longer redirects to edit_trip)
=======
>>>>>>> parent of a7ff2b5 (it no longer redirects to edit_trip)
