$(document).ready(function() {
    // Initialize the map
    var map = L.map('myMap').setView([37.3059, -89.5181], 10);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a marker
    var marker = L.marker([37.3059, -89.5181]).addTo(map);
    
    // Optionally, add a popup to the marker
    marker.bindPopup("<b>I.T. Made Easy</b><br>Serving the united states").openPopup();
});
