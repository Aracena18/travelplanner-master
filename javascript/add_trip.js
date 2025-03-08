document.addEventListener('DOMContentLoaded', function() {
    // Global variable to track the selected hotel
    let selectedHotelId = null;

    // Initialize hotel carousel if the element exists
    const hotelCarouselEl = document.querySelector('#hotel-carousel');
    if (hotelCarouselEl) {
        const hotelCarousel = new bootstrap.Carousel(hotelCarouselEl, {
            interval: false // Disable auto sliding
        });
    } else {
        console.error('Hotel carousel element not found.');
    }

    // Function to populate hotel cards
    function populateHotelCards(hotels) {
        const hotelGrid = document.getElementById('hotel-grid');
        if (!hotelGrid) {
            console.error('Hotel grid element not found.');
            return;
        }
        hotelGrid.innerHTML = ''; // Clear existing content

        hotels.forEach((hotel, index) => {
            const card = `
                <div class="hotel-card ${hotel.id === selectedHotelId ? 'selected' : ''}" data-hotel-id="${hotel.id}">
                    <img src="${hotel.image}" class="hotel-image" alt="${hotel.name}">
                    <div class="hotel-badge">${hotel.rating} Stars</div>
                    <div class="hotel-content">
                        <h5 class="hotel-name">${hotel.name}</h5>
                        <div class="hotel-rating">
                            ${generateStars(hotel.rating)}
                        </div>
                        <div class="hotel-amenities">
                            ${generateAmenityTags(hotel.amenities)}
                        </div>
                        <div class="hotel-price">
                            $${hotel.price} / night
                        </div>
                        <div class="hotel-actions">
                            <button class="btn btn-primary select-hotel" data-hotel-id="${hotel.id}">
                                Select Hotel
                            </button>
                            <button class="btn btn-outline-primary view-details" data-hotel-id="${hotel.id}">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
            hotelGrid.innerHTML += card;
        });

        // Add event listeners to hotel cards' select buttons
        document.querySelectorAll('.select-hotel').forEach(button => {
            button.addEventListener('click', function() {
                const hotelId = this.dataset.hotelId;
                selectHotel(hotelId);
            });
        });
    }

    // Helper function to generate star rating
    function generateStars(rating) {
        return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    }

    // Helper function to generate amenity tags
    function generateAmenityTags(amenities) {
        return amenities.map(amenity => `
            <span class="amenity-tag">
                <i class="fas ${getAmenityIcon(amenity)}"></i>
                ${amenity}
            </span>
        `).join('');
    }

    // Helper function to get amenity icons
    function getAmenityIcon(amenity) {
        const icons = {
            'Pool': 'fa-swimming-pool',
            'Spa': 'fa-spa',
            'WiFi': 'fa-wifi',
            'Restaurant': 'fa-utensils',
            'Gym': 'fa-dumbbell',
            'Beach Access': 'fa-umbrella-beach'
        };
        return icons[amenity] || 'fa-concierge-bell';
    }

    // Define a function to handle hotel selection
    function selectHotel(hotelId) {
        selectedHotelId = hotelId;
        console.log('Selected hotel ID:', hotelId);
        // Re-populate the hotel cards to update the "selected" visual state
        if (window.hotelsData) {
            populateHotelCards(window.hotelsData);
        }
        // Additional logic for handling a hotel selection (such as updating a hidden form field) can go here.
    }

    // Filter functionality for filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const filter = this.textContent.trim();
            // Implement filtering logic here. For now, we just log the filter.
            console.log('Filter selected:', filter);
        });
    });

    // Example: If hotel data is available globally (e.g., injected by PHP), populate the hotel cards.
    if (window.hotelsData && Array.isArray(window.hotelsData)) {
        populateHotelCards(window.hotelsData);
    } else {
        console.warn('No hotel data available. Please define window.hotelsData as an array of hotels.');
    }
});
