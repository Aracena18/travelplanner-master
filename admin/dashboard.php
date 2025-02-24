<?php
session_start();
require_once '../db.php';
require_once 'auth_middleware.php';

// Check admin authentication
checkAdminAuth();

// Get some basic statistics with error checking
$users_count = 0;
$trips_count = 0;

$users_result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($users_result) {
    $users_count = $users_result->fetch_assoc()['count'];
} else {
    echo "Error getting users count: " . $conn->error;
}

$trips_result = $conn->query("SELECT COUNT(*) as count FROM trips");
if ($trips_result) {
    $trips_count = $trips_result->fetch_assoc()['count'];
} else {
    echo "Error getting trips count: " . $conn->error;
}

$hotels_query = "SELECT * FROM hotels ORDER BY created_at DESC LIMIT 10";
$hotels_result = $conn->query($hotels_query);

// Add debugging information
echo "<!-- Debug Info: -->";
echo "<!-- Database connected: " . ($conn ? "Yes" : "No") . " -->";
echo "<!-- Users count: $users_count -->";
echo "<!-- Trips count: $trips_count -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            background: #f8f9fa;
        }

        .admin_page {
            display: flex;
        }

        aside {
            height: 100vh;
            width: 280px;
            background: #ffffff;
            position: fixed;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border-right: 1px solid #eaeaea;
        }

        .sidenav-header {
            padding: 0 0.5rem 1.5rem 0.5rem;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 1.5rem;
        }

        .sidenav-header h4 {
            color: #1a1a1a;
            font-weight: 600;
            margin: 0;
        }

        .sidenav ul {
            list-style: none;
            padding: 0;
        }

        .sidenav ul li {
            margin-bottom: 0.5rem;
        }

        .sidenav ul li a {
            color: #4a5568;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidenav ul li a:hover {
            background: #f8f9fa;
            color: #2563eb;
        }

        .sidenav ul li a i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .sidenav ul li a.active {
            background: #EEF2FF;
            color: #2563eb;
        }

        #content {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 2rem;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a href="../auth/logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

<section class="admin_page">
<aside>
    <div class="sidenav-header">
        <h4>Admin Panel</h4>
    </div>
    <nav class="sidenav">
        <ul>
            <li>
                <a href="dashboard.php" class="active">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="hotels.php">
                    <i class="fas fa-hotel"></i>
                    Hotels
                </a>
            </li>
            <li>
                <a href="flights.php">
                    <i class="fas fa-plane-departure"></i>
                    Flights
                </a>
            </li>
            <li>
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    Users
                </a>
            </li>
            <li>
                <a href="recommendations.php">
                    <i class="fas fa-star"></i>
                    Recommendations
                </a>
            </li>
            <li>
                <a href="explores.php">
                    <i class="fas fa-compass"></i>
                    Explores
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>

    <div class="container mt-4" id="content">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4"><?php echo $users_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Trips</h5>
                        <p class="card-text display-4"><?php echo $trips_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="hotelbooking mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Hotel Bookings</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal">
            <i class="fas fa-plus"></i> Add Hotel
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hotel Name</th>
                            <th>Location</th>
                            <th>Price/Night</th>
                            <th>Available Rooms</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hotelsTableBody">
                        <?php if($hotels_result && $hotels_result->num_rows > 0): ?>
                            <?php while($hotel = $hotels_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                                    <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                                    <td>$<?php echo number_format($hotel['price_per_night'], 2); ?></td>
                                    <td><?php echo $hotel['available_rooms']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $hotel['status'] === 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($hotel['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editHotelModal<?php echo $hotel['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteHotel(<?php echo $hotel['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hotels found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update the Add Hotel Modal form -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addHotelForm" onsubmit="return submitHotelForm(event)">
                <div class="modal-body">
                    <div id="formAlert" class="alert" style="display: none;"></div>
                    <div class="mb-3">
                        <label for="hotelName" class="form-label">Hotel Name</label>
                        <input type="text" class="form-control" id="hotelName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price per Night</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="rooms" class="form-label">Available Rooms</label>
                        <input type="number" class="form-control" id="rooms" name="rooms" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function deleteHotel(hotelId) {
    if(confirm('Are you sure you want to delete this hotel?')) {
        fetch('delete_hotel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: hotelId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error deleting hotel');
            }
        });
    }
}

// Add this to the bottom of dashboard.php before </body>
document.addEventListener('DOMContentLoaded', function() {
    // Get the hotels link
    const hotelsLink = document.querySelector('a[href="hotels.php"]');
    
    // Prevent default navigation
    hotelsLink.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Load hotels content into main content area
        const contentDiv = document.getElementById('content');
        
        // Update content
        contentDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Hotel Management</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal">
                    <i class="fas fa-plus"></i> Add Hotel
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hotel Name</th>
                                    <th>Location</th>
                                    <th>Price/Night</th>
                                    <th>Available Rooms</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="hotelsTableBody">
                                <!-- Hotels data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;

        // Load hotels data
        fetch('get_hotels.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('hotelsTableBody');
                if (data.length > 0) {
                    tableBody.innerHTML = data.map(hotel => `
                        <tr>
                            <td>${hotel.name}</td>
                            <td>${hotel.location}</td>
                            <td>$${parseFloat(hotel.price_per_night).toFixed(2)}</td>
                            <td>${hotel.available_rooms}</td>
                            <td>
                                <span class="badge bg-${hotel.status === 'active' ? 'success' : 'warning'}">
                                    ${hotel.status}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editHotel(${hotel.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteHotel(${hotel.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hotels found</td></tr>';
                }
            });

        // Update active state in sidebar
        document.querySelectorAll('.sidenav ul li a').forEach(link => {
            link.classList.remove('active');
        });
        hotelsLink.classList.add('active');
    });
});

// Update the submitHotelForm function
async function submitHotelForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('addHotelForm');
    const formAlert = document.getElementById('formAlert');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    try {
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        submitButton.disabled = true;
        formAlert.style.display = 'none';

        const formData = new FormData(form);
        
        const response = await fetch('add_hotel.php', {
            method: 'POST',
            body: formData
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('Invalid content type:', contentType);
            throw new Error('Server returned invalid response format');
        }

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to add hotel');
        }

        // Success handling
        formAlert.className = 'alert alert-success';
        formAlert.textContent = data.message;
        formAlert.style.display = 'block';
        
        // Reset form and refresh table
        form.reset();
        await refreshHotelsTable();
        
        // Close modal after success
        const modal = bootstrap.Modal.getInstance(document.getElementById('addHotelModal'));
        if (modal) {
            setTimeout(() => modal.hide(), 1500);
        }

    } catch (error) {
        console.error('Operation failed:', error);
        formAlert.className = 'alert alert-danger';
        formAlert.textContent = error.message;
        formAlert.style.display = 'block';
    } finally {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }

    return false;
}

// Add this helper function to refresh the hotels table
async function refreshHotelsTable() {
    try {
        const response = await fetch('get_hotels.php');
        if (!response.ok) throw new Error('Failed to fetch hotels');
        
        const data = await response.json();
        if (!data.success) throw new Error(data.message);

        const tableBody = document.querySelector('#hotelsTableBody');
        if (!data.data.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hotels found</td></tr>';
            return;
        }

        tableBody.innerHTML = data.data.map(hotel => `
            <tr>
                <td>${escapeHtml(hotel.name)}</td>
                <td>${escapeHtml(hotel.location)}</td>
                <td>$${parseFloat(hotel.price_per_night).toFixed(2)}</td>
                <td>${hotel.available_rooms}</td>
                <td>
                    <span class="badge bg-${hotel.status === 'active' ? 'success' : 'warning'}">
                        ${escapeHtml(hotel.status)}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editHotel(${hotel.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteHotel(${hotel.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

    } catch (error) {
        console.error('Failed to refresh table:', error);
        alert('Error refreshing hotel list: ' + error.message);
    }
}

// Helper function to prevent XSS
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
</body>
</html>