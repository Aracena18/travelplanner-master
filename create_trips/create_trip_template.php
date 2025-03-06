<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Trip</title>
  <!-- External CSS libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="create_trip.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <div class="layout-container">
    <div class="planning-section">
      <h1 class="section-title">Plan Your Journey</h1>
      <form method="POST" class="trip-form">
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
                  <button class="carousel-control-prev" type="button" data-bs-target="#hotel-carousel" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#hotel-carousel" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                  </button>
              </div>
              <input type="hidden" id="hotel" name="hotel">
              <div class="col-md-6">
                  <label for="adults_num" class="form-label">No. of Adults</label>
                  <input type="text" id="adults_num" name="adults_num" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6">
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
          <div class="estimated-cost text-center">
              <h4 class="mb-0">Estimated Trip Cost</h4>
              <div class="fs-2 fw-bold" id="estimated-cost-display">$0.00</div>
          </div>
          <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-primary btn-create">
                  <i class="fas fa-plane-departure me-2"></i>Create Trip Plan
              </button>
              <a href="index.php" class="btn btn-secondary">
                  <i class="fas fa-times me-2"></i>Cancel
              </a>
          </div>
      </form>
    </div>
    <div class="map-section">
      <div id="map"></div>
    </div>
  </div>
  
  <!-- External JS libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- Pass PHP variables to JavaScript -->
  <script>
    const hotels = <?php echo json_encode($destinations); ?>;
    const activities = <?php echo json_encode($activities); ?>;
    const tripId = <?php echo json_encode($trip_id); ?>;
  </script>
  <!-- Custom JS file -->
  <script src="create_trip.js"></script>
</body>
</html>
