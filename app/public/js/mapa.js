function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 8
    });

    google.maps.event.addListener(map, 'click', function(event) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: event.latLng }, function(results, status) {
            if (status === 'OK') {
                const pais = results[0].address_components[3].long_name;
                const ciudad = results[0].address_components[2].long_name;
                document.getElementById('pais').value = pais;
                document.getElementById('ciudad').value = ciudad;
            }
        });
    });
}