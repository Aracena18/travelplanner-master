class TripMap {
    constructor(mapId) {
        this.map = L.map(mapId, {
            zoomControl: false
        });
        this.markers = [];
        this.routeLine = null;
        
        L.control.zoom({
            position: 'bottomright'
        }).addTo(this.map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
    }

    addMarker(lat, lng, title, icon) {
        const marker = L.marker([lat, lng], {
            icon: L.divIcon({
                html: `<i class="fas fa-${icon}"></i>`,
                className: 'custom-map-marker',
                iconSize: [30, 30]
            })
        }).bindPopup(title);
        
        this.markers.push(marker);
        marker.addTo(this.map);
        return marker;
    }

    drawRoute() {
        if (this.markers.length < 2) return;
        
        const points = this.markers.map(marker => marker.getLatLng());
        if (this.routeLine) {
            this.map.removeLayer(this.routeLine);
        }
        
        this.routeLine = L.polyline(points, {
            color: '#2563eb',
            weight: 3,
            opacity: 0.8,
            dashArray: '10, 10'
        }).addTo(this.map);
        
        this.map.fitBounds(L.latLngBounds(points).pad(0.1));
    }

    clearMap() {
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
        if (this.routeLine) {
            this.map.removeLayer(this.routeLine);
            this.routeLine = null;
        }
    }
}
