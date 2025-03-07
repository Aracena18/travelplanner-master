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
  <!-- Choices.js CSS for searchable dropdown -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
</head>

<body>
  <div class="layout-container">
    <div class="planning-section">
      <div class="header-banner">
        <h1 class="display-4">
          <i class="fas fa-globe-americas me-3"></i>
          Plan Your Dream Journey
        </h1>
        <p class="lead mb-0">Create unforgettable memories with our travel planner</p>
      </div>

      <div class="progress-bar-container">
        <div class="progress-steps">
          <div class="step-indicator step-active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Destination</div>
          </div>
          <div class="step-indicator" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Accommodation</div>
          </div>
          <div class="step-indicator" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Details</div>
          </div>
          <div class="step-indicator" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Review</div>
          </div>
        </div>
      </div>

      <form id="trip-form" method="POST" class="trip-form">
        <div class="form-step active" data-step="1">
          <!-- Hidden Trip Name field, auto-populated from the selected destination -->
          <input type="hidden" id="trip_name" name="trip_name">
          <div class="row g-3">
            <div class="col-12">
              <div class="destination-select-container">
                <label for="destination" class="form-label">
                  <i class="fas fa-map-marker-alt me-2"></i>Choose Your Destination
                </label>
                <select id="destination" name="destination" class="form-select" required>
                  <option value="">Select a destination...</option>
                  <?php foreach ($destinations as $location_id => $location): ?>
                    <option value="<?= $location_id ?>"><?= htmlspecialchars($location['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="button" class="btn btn-primary" id="step1-continue">Continue</button>
          </div>
        </div>

        <div class="form-step" data-step="2">
          <div class="form-section">
            <h3 class="section-title mb-4">
              <i class="fas fa-hotel section-icon"></i>
              Select Your Accommodation
            </h3>
            <div id="hotel-carousel" class="carousel slide mb-4" data-bs-ride="false">
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
            <input type="hidden" id="hotel" name="hotel">
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="button" class="btn btn-primary" id="step2-continue">Continue</button>
          </div>
        </div>

        <div class="form-step" data-step="3">
          <div class="form-section">
            <h3 class="section-title mb-4">
              <i class="fas fa-users section-icon"></i>
              Travel Details
            </h3>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" id="adults_num" name="adults_num" class="form-control"
                    required>
                  <label for="adults_num">Number of Adults</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" id="childs_num" name="childs_num" class="form-control"
                    required>
                  <label for="childs_num">Number of Children</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" id="start_date" name="start_date" class="form-control" required>
                  <label for="start_date">Start Date</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" id="end_date" name="end_date" class="form-control" required>
                  <label for="end_date">End Date</label>
                </div>
              </div>
              <!-- Reservations and Attachments -->
              <div class="col-12 mt-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title">Reservations and attachments</h5>
                    <div class="d-flex flex-wrap gap-2">
                      <?php
                      include 'sub_plans_options.php';
                      ?>
                    </div>
                  </div>
                </div>

                <!-- Budgeting -->
                <div class="card mb-3">
                  <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0">Budgeting</h5>
                    </div>
                    <div>
                      <span class="fs-5 fw-bold">$0.00</span>
                      <a href="#" class="ms-3 text-decoration-none">View details</a>
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Notes</h5>
                    <textarea class="form-control" rows="4" name="notes"
                      placeholder="Write or paste anything here: how to get around, tips and tricks"></textarea>
                  </div>
                </div>
              </div>
              <input type="hidden" name="step3" value="1">
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="button" class="btn btn-primary next-step">Review</button>
          </div>
        </div>

        <div class="form-step" data-step="4">
          <div class="trip-summary">
            <h3>Trip Summary</h3>
            <div class="estimated-cost text-center">
              <h4 class="mb-0">Estimated Trip Cost</h4>
              <div class="fs-2 fw-bold" id="estimated-cost-display">$0.00</div>
            </div>
            <div id="summary-content" class="mt-4">
              <!-- Will be populated via JavaScript -->
            </div>
          </div>
          <div class="navigation-buttons">
            <button type="button" class="btn btn-secondary prev-step">Back</button>
            <button type="submit" class="btn btn-create">
              <i class="fas fa-plane-departure me-2"></i>Confirm Booking
            </button>
          </div>
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
  <!-- Choices.js JS for searchable dropdown -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- Pass PHP variables to JavaScript -->
  <script>
    const hotels = <?php echo json_encode($destinations); ?>;
    const activities = <?php echo json_encode($activities); ?>;
    const tripId = <?php echo json_encode($trip_id); ?>;
    const currentStep = <?php echo json_encode(isset($_GET['step']) ? $_GET['step'] : 1); ?>;
  </script>

  <!-- Initialize Choices.js on the destination dropdown and update the hidden trip name -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const destinationElement = document.getElementById('destination');
      const tripNameInput = document.getElementById('trip_name');

      // Initialize Choices.js
      const choices = new Choices(destinationElement, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        placeholderValue: "Select a destination"
      });

      // Function to update the hidden trip name based on the selected destination's text
      function updateTripName() {
        const selectedOption = destinationElement.options[destinationElement.selectedIndex];
        if (selectedOption) {
          tripNameInput.value = selectedOption.text.trim();
        }
      }

      // Set initial trip name value
      updateTripName();

      // Update trip name whenever the destination changes
      destinationElement.addEventListener('change', function() {
        updateTripName();
      });
    });
  </script>

  <!-- Custom JS file -->
  <script src="create_trip.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const steps = document.querySelectorAll('.form-step');
      const indicators = document.querySelectorAll('.step-indicator');
      let currentStep = parseInt(new URLSearchParams(window.location.search).get('step')) || 1;

      function showStep(stepNumber) {
        steps.forEach(step => step.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('step-active'));

        const activeStep = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        const activeIndicator = document.querySelector(`.step-indicator[data-step="${stepNumber}"]`);

        activeStep.classList.add('active');
        activeIndicator.classList.add('step-active');

        // Update progress
        indicators.forEach(indicator => {
          const step = parseInt(indicator.dataset.step);
          if (step < currentStep) {
            indicator.classList.add('step-completed');
          } else {
            indicator.classList.remove('step-completed');
          }
        });

        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('step', stepNumber);
        window.history.pushState({}, '', url);
      }

      // Show the current step on page load
      showStep(currentStep);

      // Navigation button handlers
      document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', () => {
          if (currentStep < 4) {
            currentStep++;
            showStep(currentStep);
          }
        });
      });

      document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', () => {
          if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
          }
        });
      });

      // Make step numbers clickable
      indicators.forEach(indicator => {
        indicator.addEventListener('click', () => {
          const step = parseInt(indicator.dataset.step);
          currentStep = step;
          showStep(currentStep);
        });
      });

      // AJAX for step 1
      document.getElementById('step1-continue').addEventListener('click', function() {
        const destination = document.getElementById('destination').value;
        if (destination) {
          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'create_trip_ajax.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              if (response.success) {
                currentStep++;
                showStep(currentStep);
              } else {
                alert('Failed to create trip. Please try again.');
              }
            }
          };
          xhr.send('step1=1&destination=' + destination + '&trip_id=' + tripId);
        } else {
          alert('Please select a destination.');
        }
      });

      // AJAX for step 2
      document.getElementById('step2-continue').addEventListener('click', function() {
        const activeHotelItem = document.querySelector('#hotel-cards .carousel-item.active');
        if (activeHotelItem) {
          const hotelName = activeHotelItem.getAttribute('data-hotel');
          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'update_hotel_ajax.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              if (response.success) {
                currentStep++;
                showStep(currentStep);
              } else {
                alert('Failed to update hotel. Please try again.');
              }
            }
          };
          xhr.send('step2=1&hotel=' + encodeURIComponent(hotelName) + '&trip_id=' + tripId);
        } else {
          alert('Please select a hotel.');
        }
      });

      // AJAX for step 3
      function updateTravelDetails(field, value) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_travel_details_ajax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (!response.success) {
              alert('Failed to update travel details. Please try again.');
            }
          }
        };
        xhr.send('step3=1&field=' + field + '&value=' + value + '&trip_id=' + tripId);
      }

      document.getElementById('adults_num').addEventListener('change', function() {
        updateTravelDetails('adults_num', this.value);
      });

      document.getElementById('childs_num').addEventListener('change', function() {
        updateTravelDetails('childs_num', this.value);
      });

      document.getElementById('start_date').addEventListener('change', function() {
        updateTravelDetails('start_date', this.value);
      });

      document.getElementById('end_date').addEventListener('change', function() {
        updateTravelDetails('end_date', this.value);
      });
    });
  </script>
</body>

</html>