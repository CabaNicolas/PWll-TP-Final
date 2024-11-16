document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([-34.6037345, -58.3841453], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let marker = L.marker([-34.6037345, -58.3841453], { draggable: true }).addTo(map);

    const actualizarCamposCoordenadas = (lat, lng) => {
        document.getElementById('lat').value = lat.toFixed(8); // Máxima precisión
        document.getElementById('long').value = lng.toFixed(8);
    };

    marker.on('dragend', function (event) {
        const position = event.target.getLatLng();
        actualizarCamposCoordenadas(position.lat, position.lng);
    });

    map.on('click', function (e) {
        const position = e.latlng;
        marker.setLatLng(position);
        actualizarCamposCoordenadas(position.lat, position.lng);
    });
});