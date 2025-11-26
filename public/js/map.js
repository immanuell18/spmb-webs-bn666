// Map functionality for SPMB application
class SPMBMap {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.map = null;
        this.markers = [];
        this.markerCluster = null;
        this.heatmapLayer = null;
        this.options = {
            center: [-6.2088, 106.8456], // Jakarta default
            zoom: 10,
            ...options
        };
        this.init();
    }

    init() {
        // Initialize Leaflet map
        this.map = L.map(this.containerId).setView(this.options.center, this.options.zoom);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        // Initialize marker cluster group
        this.markerCluster = L.markerClusterGroup({
            chunkedLoading: true,
            maxClusterRadius: 50
        });
        this.map.addLayer(this.markerCluster);
    }

    // Load markers from API
    async loadMarkers(filters = {}) {
        try {
            const response = await fetch('/api/map/data?' + new URLSearchParams(filters));
            const data = await response.json();
            
            this.clearMarkers();
            
            data.markers.forEach(marker => {
                this.addMarker(marker);
            });

            // Update statistics if callback provided
            if (this.options.onStatisticsUpdate) {
                this.options.onStatisticsUpdate(data.statistics);
            }

            return data;
        } catch (error) {
            console.error('Error loading markers:', error);
        }
    }

    // Add single marker
    addMarker(markerData) {
        const marker = L.marker([markerData.lat, markerData.lng])
            .bindPopup(markerData.popup_content);

        // Custom icon based on status
        const iconColor = this.getStatusColor(markerData.status);
        marker.setIcon(this.createCustomIcon(iconColor));

        this.markerCluster.addLayer(marker);
        this.markers.push(marker);
    }

    // Create custom icon
    createCustomIcon(color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
    }

    // Get color based on status
    getStatusColor(status) {
        const colors = {
            'SUBMIT': '#ffc107',      // warning
            'ADM_PASS': '#17a2b8',    // info
            'ADM_REJECT': '#dc3545',  // danger
            'PAID': '#28a745'         // success
        };
        return colors[status] || '#6c757d';
    }

    // Clear all markers
    clearMarkers() {
        this.markerCluster.clearLayers();
        this.markers = [];
    }

    // Load heatmap
    async loadHeatmap(filters = {}) {
        try {
            const response = await fetch('/api/map/heatmap?' + new URLSearchParams(filters));
            const data = await response.json();
            
            if (this.heatmapLayer) {
                this.map.removeLayer(this.heatmapLayer);
            }

            this.heatmapLayer = L.heatLayer(data, {
                radius: 25,
                blur: 15,
                maxZoom: 17
            }).addTo(this.map);

        } catch (error) {
            console.error('Error loading heatmap:', error);
        }
    }

    // Toggle heatmap
    toggleHeatmap() {
        if (this.heatmapLayer) {
            if (this.map.hasLayer(this.heatmapLayer)) {
                this.map.removeLayer(this.heatmapLayer);
            } else {
                this.map.addLayer(this.heatmapLayer);
            }
        }
    }

    // Fit bounds to markers
    fitBounds() {
        if (this.markers.length > 0) {
            const group = new L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Add click handler for coordinate picking
    enableCoordinatePicker(callback) {
        this.map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            callback(lat, lng);
        });
    }

    // Set marker at specific location
    setPickerMarker(lat, lng) {
        if (this.pickerMarker) {
            this.map.removeLayer(this.pickerMarker);
        }
        
        this.pickerMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'picker-marker',
                html: '<div style="background-color: red; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white;"></div>',
                iconSize: [15, 15],
                iconAnchor: [7.5, 7.5]
            })
        }).addTo(this.map);

        this.map.setView([lat, lng], 15);
    }
}

// Coordinate picker functionality
let coordinateMap = null;

function initCoordinatePicker() {
    if (document.getElementById('coordinate-map')) {
        coordinateMap = new SPMBMap('coordinate-map', {
            center: [-6.2088, 106.8456],
            zoom: 12
        });

        // Enable coordinate picking
        coordinateMap.enableCoordinatePicker((lat, lng) => {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            coordinateMap.setPickerMarker(lat, lng);
            
            // Reverse geocode to validate
            reverseGeocode(lat, lng);
        });

        // Load existing coordinates if available
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        if (lat && lng) {
            coordinateMap.setPickerMarker(parseFloat(lat), parseFloat(lng));
        }
    }
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                
                if (coordinateMap) {
                    coordinateMap.setPickerMarker(lat, lng);
                }
                
                reverseGeocode(lat, lng);
            },
            (error) => {
                alert('Error getting location: ' + error.message);
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Geocode address to coordinates
async function geocodeAddress() {
    const alamat = document.getElementById('alamat').value;
    if (!alamat) {
        alert('Silakan isi alamat terlebih dahulu');
        return;
    }

    try {
        const response = await fetch('/api/map/geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ address: alamat })
        });

        const data = await response.json();
        
        if (data.success) {
            document.getElementById('latitude').value = data.lat.toFixed(6);
            document.getElementById('longitude').value = data.lng.toFixed(6);
            
            if (coordinateMap) {
                coordinateMap.setPickerMarker(data.lat, data.lng);
            }
            
            alert('Koordinat berhasil ditemukan!');
        } else {
            alert('Alamat tidak ditemukan: ' + data.message);
        }
    } catch (error) {
        alert('Error geocoding: ' + error.message);
    }
}

// Reverse geocode coordinates to address
async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch('/api/map/reverse-geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lat, lng })
        });

        const data = await response.json();
        
        if (data.success) {
            console.log('Reverse geocoded address:', data.address);
        }
    } catch (error) {
        console.error('Error reverse geocoding:', error);
    }
}

// Admin map functionality
class AdminMap extends SPMBMap {
    constructor(containerId, options = {}) {
        super(containerId, options);
        this.setupControls();
    }

    setupControls() {
        // Add layer control
        const baseLayers = {
            "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
            "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
        };

        L.control.layers(baseLayers).addTo(this.map);

        // Add custom controls
        this.addCustomControls();
    }

    addCustomControls() {
        // Heatmap toggle control
        const heatmapControl = L.control({ position: 'topright' });
        heatmapControl.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = '<a href="#" title="Toggle Heatmap"><i class="fas fa-fire"></i></a>';
            div.onclick = (e) => {
                e.preventDefault();
                this.toggleHeatmap();
            };
            return div;
        };
        heatmapControl.addTo(this.map);

        // Fit bounds control
        const fitBoundsControl = L.control({ position: 'topright' });
        fitBoundsControl.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = '<a href="#" title="Fit to Markers"><i class="fas fa-expand-arrows-alt"></i></a>';
            div.onclick = (e) => {
                e.preventDefault();
                this.fitBounds();
            };
            return div;
        };
        fitBoundsControl.addTo(this.map);
    }
}

// Initialize maps when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize coordinate picker if on registration form
    if (document.getElementById('coordinate-map')) {
        // Load Leaflet CSS and JS
        if (!document.querySelector('link[href*="leaflet"]')) {
            const leafletCSS = document.createElement('link');
            leafletCSS.rel = 'stylesheet';
            leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(leafletCSS);

            const leafletJS = document.createElement('script');
            leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            leafletJS.onload = initCoordinatePicker;
            document.head.appendChild(leafletJS);
        } else {
            initCoordinatePicker();
        }
    }
});

// Export for use in other scripts
window.SPMBMap = SPMBMap;
window.AdminMap = AdminMap;