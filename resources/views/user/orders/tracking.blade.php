<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">Lacak Pesanan <span class="text-blue-600">#{{ $order->order_number }}</span></h1>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <p><strong>Status:</strong> <span id="order-status" class="font-semibold text-green-600">{{ $order->status->label() }}</span></p>
                <p><strong>Alamat Pengiriman:</strong> {{ $order->shipping_address }}</p>
            </div>

            <div id="map" class="w-full h-96 rounded-lg border"></div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap&libraries=geometry" defer async></script>

    <script>
    let map;
    let deliveryMarker;

    function initMap() {
        const storeLocation = { lat: {{ $storeLocation['lat'] }}, lng: {{ $storeLocation['lng'] }} };
        const userLocation = { lat: {{ (float) $userAddress->latitude }}, lng: {{ (float) $userAddress->longitude }} };
        
        map = new google.maps.Map(document.getElementById("map"), {
            center: storeLocation,
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
        });

        const storeIcon = {
            url: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSK_3JRzhuY93mBAU8DoSToYa4YDhgR8OOjUQ&s', 
            scaledSize: new google.maps.Size(40, 40),
        };

        const homeIcon = {
            url: 'https://cdn-icons-png.flaticon.com/512/619/619153.png', 
            scaledSize: new google.maps.Size(40, 40),
        };

        new google.maps.Marker({
            position: storeLocation,
            map: map,
            title: "Lokasi Toko",
            icon: storeIcon
        });

        new google.maps.Marker({
            position: userLocation,
            map: map,
            title: "Alamat Anda",
            icon: homeIcon
        });
        
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#A8B3C4',
                strokeWeight: 5,
                strokeOpacity: 0.8,
            }
        });
        directionsRenderer.setMap(map);

        calculateAndDisplayRoute(directionsService, directionsRenderer, storeLocation, userLocation);
    }

    function calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination) {
        directionsService.route(
            {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
            },
            (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    const routePath = response.routes[0].overview_path;
                    simulateDelivery(routePath);
                } else {
                    window.alert("Gagal menampilkan rute: " + status);
                }
            }
        );
    }

    function simulateDelivery(path) {
        const courierIcon = {
            url: 'https://static.thenounproject.com/png/2526085-200.png',
            scaledSize: new google.maps.Size(45, 45),
            anchor: new google.maps.Point(22, 22), 
        };

        deliveryMarker = new google.maps.Marker({
            position: path[0],
            map: map,
            icon: courierIcon,
            title: "Kurir Anda"
        });

        const traveledPathPolyline = new google.maps.Polyline({
            path: [path[0]],
            strokeColor: '#2563EB',
            strokeOpacity: 1.0,
            strokeWeight: 6,
            map: map
        });

        let pathIndex = 0;
        let animationStep = 0;
        const stepsPerSegment = 60; 
        const animationSpeed = 20;

        setTimeout(() => {
            document.querySelector('#order-status').textContent = 'Kurir sedang menuju lokasimu';
        }, 1500);
        
        const animationInterval = setInterval(() => {
            if (animationStep >= stepsPerSegment) {
                animationStep = 0;
                pathIndex++;
                if (pathIndex >= path.length - 1) {
                    clearInterval(animationInterval);
                    deliveryMarker.setPosition(path[path.length - 1]);
                    traveledPathPolyline.getPath().push(path[path.length - 1]);
                    document.querySelector('#order-status').textContent = 'Pesanan Telah Tiba 🎉';
                    deliveryMarker.setTitle("Pesanan Tiba!");
                    return;
                }
            }
            
            animationStep++;
            const progress = animationStep / stepsPerSegment;
            const startPoint = path[pathIndex];
            const endPoint = path[pathIndex + 1];
            const newPosition = google.maps.geometry.spherical.interpolate(startPoint, endPoint, progress);
            deliveryMarker.setPosition(newPosition);
            traveledPathPolyline.getPath().push(newPosition);
            map.panTo(newPosition);
        }, animationSpeed);
    }
    </script>
</x-layouts.plain-app>
