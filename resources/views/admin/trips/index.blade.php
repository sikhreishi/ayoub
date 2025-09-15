@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
    <style>
        #map {
            height: 450px;
            width: 100%;
            border-radius: 8px;
        }

        .driver-info {
            /* background: #f8f9fa; */
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }

        .location-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-online {
            background-color: #d4edda;
            color: #155724;
        }

        .status-offline {
            background-color: #f8d7da;
            color: #721c24;
        }

        .firebase-data {
            /* background: #e3f2fd; */
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
            border-left: 3px solid #2196f3;
        }

        .realtime-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #4caf50;
            border-radius: 50%;
            animation: pulse 2s infinite;
            margin-right: 5px;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
@endpush
@extends('layouts.app')
@section('content')
<div class="table-responsive">
    <x-data-table
        title="{{ __('dashboard.trips.trips_management') }}"
        table-id="trips-table"
        fetch-url="{{ route('admin.trips.byStatus') }}{{ isset($status) && $status ? '?status=' . $status : '' }}"
        :columns="[
            __('dashboard.trips.id'),
            __('dashboard.trips.user_name'),
            __('dashboard.trips.driver_name'),
            __('dashboard.trips.status'),
            __('dashboard.trips.estimated_fare'),
            __('dashboard.trips.final_fare'),
            __('dashboard.trips.payment_status'),
            __('dashboard.trips.payment_method'),
            __('dashboard.trips.requested_at'),
            __('dashboard.trips.completed_at'),
            __('dashboard.trips.actions'),
        ]"
        :columns-config="[
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'user_name', 'name' => 'user_name'],
            ['data' => 'driver_name', 'name' => 'driver_name'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'estimated_fare', 'name' => 'estimated_fare'],
            ['data' => 'final_fare', 'name' => 'final_fare'],
            ['data' => 'payment_status', 'name' => 'payment_status'],
            ['data' => 'payment_method', 'name' => 'payment_method'],
            ['data' => 'requested_at', 'name' => 'requested_at'],
            ['data' => 'completed_at', 'name' => 'completed_at'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
        ]"
    />
</div>

<!-- Driver Location Modal -->
<div class="modal fade" id="driverLocationModal" tabindex="-1" aria-labelledby="driverLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="driverLocationModalLabel">
                    <i class="material-icons-outlined me-2">location_on</i>
                    {{ __('dashboard.trips.driver_location') }}
                    <span class="realtime-indicator"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('dashboard.trips.close') }}"></button>
            </div>
            <div class="modal-body p-0">
                <div id="driverInfo" class="driver-info m-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="material-icons-outlined me-1">person</i>{{ __('dashboard.trips.driver_info') }}</h6>
                            <p><strong>{{ __('dashboard.trips.name') }}:</strong> <span id="driverName"></span></p>
                            <p><strong>{{ __('dashboard.trips.phone') }}:</strong> <span id="driverPhone"></span></p>
                            <p><strong>{{ __('dashboard.trips.status') }}:</strong> <span id="driverStatus" class="location-status"></span></p>
                            <p><strong>{{ __('dashboard.trips.last_update') }}:</strong> <span id="lastUpdate"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="material-icons-outlined me-1">route</i>{{ __('dashboard.trips.trip_details') }}</h6>
                            <p><strong>{{ __('dashboard.trips.trip_id') }}:</strong> <span id="tripId"></span></p>
                            <p><strong>{{ __('dashboard.trips.distance_to_pickup') }}:</strong> <span id="distanceToPickup"></span></p>
                            <p><strong>{{ __('dashboard.trips.distance_to_dropoff') }}:</strong> <span id="distanceToDropoff"></span></p>
                            <p><strong>{{ __('dashboard.trips.total_trip_distance') }}:</strong> <span id="totalDistance"></span></p>
                        </div>
                    </div>

                    <div class="firebase-data">
                        <h6><i class="material-icons-outlined me-1">cloud</i>{{ __('dashboard.trips.firebase_data') }}</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small><strong>{{ __('dashboard.trips.coordinates') }}:</strong></small><br>
                                <small>{{ __('dashboard.trips.lat') }}: <span id="firebaseLat">-</span></small><br>
                                <small>{{ __('dashboard.trips.lng') }}: <span id="firebaseLng">-</span></small>
                            </div>
                            <div class="col-md-4">
                                <small><strong>{{ __('dashboard.trips.geohash') }}:</strong></small><br>
                                <small><span id="firebaseGeohash">-</span></small>
                            </div>
                            <div class="col-md-4">
                                <small><strong>{{ __('dashboard.trips.firebase_status') }}:</strong></small><br>
                                <small><span id="firebaseStatus">-</span></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="map"></div>

                <div id="loadingMap" class="text-center p-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">{{ __('dashboard.trips.loading') }}</span>
                    </div>
                    <p class="mt-3 text-muted">{{ __('dashboard.trips.loading') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons-outlined me-1">close</i>{{ __('dashboard.trips.close') }}
                </button>
                <button type="button" class="btn btn-primary" id="refreshLocation">
                    <i class="material-icons-outlined me-1">refresh</i>{{ __('dashboard.trips.refresh_location') }}
                </button>
                <button type="button" class="btn btn-success" id="autoRefresh">
                    <i class="material-icons-outlined me-1">autorenew</i>{{ __('dashboard.trips.auto_refresh') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>

    @if (request()->get('status') === 'in_progress')
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database-compat.js"></script>

        @if (env('GOOGLE_MAPS_API_KEY'))
            <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry,places">
            </script>
        @else
            <script>
                console.error('Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file');
            </script>
        @endif

        <script>
            // Firebase Configuration
            const firebaseConfig = {
                apiKey: "AIzaSyDwH1kfiDSLvjI4V4UxLqZQnIyGH87MBzw",
                authDomain: "waddini-ccbc7.firebaseapp.com",
                databaseURL: "https://waddini-ccbc7-default-rtdb.asia-southeast1.firebasedatabase.app",
                projectId: "waddini-ccbc7",
                storageBucket: "waddini-ccbc7.firebasestorage.app",
                messagingSenderId: "823593320488",
                appId: "1:823593320488:web:d17d3a8ec271fd6a85b51d",
                measurementId: "G-8KDJ0T6YXF"
            };

            // Initialize Firebase
            firebase.initializeApp(firebaseConfig);
            const database = firebase.database();

            let map;
            let driverMarker;
            let pickupMarker;
            let dropoffMarker;
            let currentTripId;
            let currentDriverId;
            let autoRefreshInterval;
            let isAutoRefreshOn = false;
            let directionsService;
            let directionsRenderer;
            let mapInitialized = false;
            let firebaseListener = null;

            $(document).ready(function() {
                // Check if Google Maps API is loaded
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    console.error('Google Maps API failed to load. Please check your API key configuration.');
                    return;
                }

                // Initialize Google Maps services
                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: '#007bff',
                        strokeWeight: 4,
                        strokeOpacity: 0.8
                    }
                });

                // Handle show driver location button click
                $(document).on('click', '.show-driver-location', function() {
                    currentTripId = $(this).data('trip-id');
                    loadDriverLocation(currentTripId);
                });

                // Handle refresh location button
                $('#refreshLocation').on('click', function() {
                    if (currentTripId) {
                        loadDriverLocation(currentTripId, true);
                    }
                });

                // Handle auto refresh toggle
                $('#autoRefresh').on('click', function() {
                    toggleAutoRefresh();
                });

                // Clear interval and Firebase listener when modal is closed
                $('#driverLocationModal').on('hidden.bs.modal', function() {
                    if (autoRefreshInterval) {
                        clearInterval(autoRefreshInterval);
                        isAutoRefreshOn = false;
                        $('#autoRefresh').html(
                                '<i class="material-icons-outlined me-1">autorenew</i>Auto Refresh: OFF')
                            .removeClass('btn-danger').addClass('btn-success');
                    }

                    // Remove Firebase listener
                    if (firebaseListener && currentDriverId) {
                        database.ref('drivers/' + currentDriverId).off('value', firebaseListener);
                        firebaseListener = null;
                    }

                    mapInitialized = false;
                    currentDriverId = null;
                });
            });

            function toggleAutoRefresh() {
                if (isAutoRefreshOn) {
                    clearInterval(autoRefreshInterval);
                    isAutoRefreshOn = false;
                    $('#autoRefresh').html('<i class="material-icons-outlined me-1">autorenew</i>Auto Refresh: OFF')
                        .removeClass('btn-danger').addClass('btn-success');
                } else {
                    autoRefreshInterval = setInterval(function() {
                        if (currentTripId) {
                            loadDriverLocation(currentTripId, true);
                        }
                    }, 10000);
                    isAutoRefreshOn = true;
                    $('#autoRefresh').html('<i class="material-icons-outlined me-1">stop</i>Auto Refresh: ON')
                        .removeClass('btn-success').addClass('btn-danger');
                }
            }

            function loadDriverLocation(tripId, refreshOnly = false) {
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    showError('Google Maps API is not available. Please check your API key configuration.');
                    return;
                }

                if (!refreshOnly && !mapInitialized) {
                    $('#loadingMap').show();
                    $('#map').hide();
                    $('#driverInfo').hide();
                }

                $.ajax({
                    url: '{{ route('admin.trips.driver.location', ':id') }}'.replace(':id', tripId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentDriverId = response.driver.id;

                            if (mapInitialized && refreshOnly) {
                                updateDriverLocationFromResponse(response);
                            } else {
                                displayDriverLocation(response);
                            }

                            // Setup Firebase real-time listener
                            setupFirebaseListener(currentDriverId);
                        } else {
                            showError(response.error || 'Failed to load driver location');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error loading driver location';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        showError(errorMsg);
                    }
                });
            }

            function setupFirebaseListener(driverId) {
                // Remove existing listener
                if (firebaseListener && currentDriverId) {
                    database.ref('drivers/' + currentDriverId).off('value', firebaseListener);
                }

                // Setup new listener
                const driverRef = database.ref('drivers/' + driverId);
                firebaseListener = driverRef.on('value', function(snapshot) {
                    const firebaseData = snapshot.val();
                    console.log('Firebase listener data:', firebaseData);

                    if (firebaseData && mapInitialized) {
                        updateDriverLocationFromFirebase(firebaseData);
                    }
                }, function(error) {
                    console.error('Firebase listener error:', error);
                });
            }

            function updateDriverLocationFromFirebase(firebaseData) {
                console.log('Firebase data received:', firebaseData);

                // Update Firebase data display with correct field names
                $('#firebaseLat').text(firebaseData.lat || '-');
                $('#firebaseLng').text(firebaseData.long || '-');
                $('#firebaseGeohash').text(firebaseData.geohash || '-');

                // Handle is_online status
                const isOnline = firebaseData.is_online === true || firebaseData.is_online === 'true';
                $('#firebaseStatus').text(isOnline ? 'Online' : 'Offline');

                // Format timestamp - Firebase stores Unix timestamp
                if (firebaseData.updated_at) {
                    const timestamp = typeof firebaseData.updated_at === 'number' ?
                        firebaseData.updated_at * 1000 : // Convert to milliseconds if it's seconds
                        new Date(firebaseData.updated_at).getTime();
                    $('#lastUpdate').text(new Date(timestamp).toLocaleString());
                }

                // Validate location data
                if (!firebaseData.lat || !firebaseData.long ||
                    firebaseData.lat === 0 || firebaseData.long === 0) {
                    console.warn('Invalid Firebase location data:', firebaseData);
                    return;
                }

                // Update driver marker position
                const newDriverLatLng = new google.maps.LatLng(
                    parseFloat(firebaseData.lat),
                    parseFloat(firebaseData.long)
                );

                if (driverMarker) {
                    driverMarker.setPosition(newDriverLatLng);
                    driverMarker.setAnimation(google.maps.Animation.BOUNCE);

                    setTimeout(function() {
                        if (driverMarker) {
                            driverMarker.setAnimation(null);
                        }
                    }, 2000);
                }

                // Update status display
                const statusClass = isOnline ? 'status-online' : 'status-offline';
                const statusText = isOnline ? 'Online' : 'Offline';
                $('#driverStatus').text(statusText).removeClass('status-online status-offline').addClass(statusClass);

                // Recalculate distances if we have pickup/dropoff coordinates
                if (pickupMarker && dropoffMarker) {
                    const pickupLatLng = pickupMarker.getPosition();
                    const dropoffLatLng = dropoffMarker.getPosition();

                    const distanceToPickup = google.maps.geometry.spherical.computeDistanceBetween(newDriverLatLng,
                        pickupLatLng);
                    const distanceToDropoff = google.maps.geometry.spherical.computeDistanceBetween(pickupLatLng,
                        dropoffLatLng);
                    const totalDistance = google.maps.geometry.spherical.computeDistanceBetween(newDriverLatLng, dropoffLatLng);

                    $('#distanceToPickup').text((distanceToPickup / 1000).toFixed(2) + ' km');
                    $('#distanceToDropoff').text((distanceToDropoff / 1000).toFixed(2) + ' km');
                    $('#totalDistance').text((totalDistance / 1000).toFixed(2) + ' km');

                    // Update directions
                    if (directionsService && directionsRenderer) {
                        directionsService.route({
                            origin: newDriverLatLng,
                            destination: dropoffLatLng,
                            waypoints: [{
                                location: pickupLatLng,
                                stopover: true
                            }],
                            travelMode: google.maps.TravelMode.DRIVING
                        }, function(result, status) {
                            if (status === 'OK') {
                                directionsRenderer.setDirections(result);
                            }
                        });
                    }
                }

                // Center map on new driver location
                if (map) {
                    map.panTo(newDriverLatLng);
                }

                // Update info window content if it exists
                updateDriverInfoWindow(firebaseData);
            }

            function updateDriverInfoWindow(firebaseData) {
                if (driverMarker && driverMarker.infoWindow) {
                    const isOnline = firebaseData.is_online === true || firebaseData.is_online === 'true';
                    const timestamp = typeof firebaseData.updated_at === 'number' ?
                        firebaseData.updated_at * 1000 :
                        new Date(firebaseData.updated_at).getTime();

                    const updatedContent = `
                        <div style="padding: 10px;">
                            <h6 style="margin: 0 0 10px 0; color: #007bff;">
                                <i class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">directions_car</i>
                                Driver Location (Live)
                            </h6>
                            <p style="margin: 5px 0;"><strong>Status:</strong>
                                <span style="color: ${isOnline ? '#28a745' : '#dc3545'};">
                                    ${isOnline ? 'Online' : 'Offline'}
                                </span>
                            </p>
                            <p style="margin: 5px 0;"><strong>Coordinates:</strong> ${firebaseData.lat}, ${firebaseData.long}</p>
                            <p style="margin: 5px 0;"><strong>Geohash:</strong> ${firebaseData.geohash}</p>
                            <small style="color: #6c757d;">Last Update: ${new Date(timestamp).toLocaleString()}</small>
                            <hr style="margin: 10px 0;">
                            <small style="color: #2196f3;"><strong>🔴 Live Firebase Data</strong></small>
                        </div>
                    `;

                    driverMarker.infoWindow.setContent(updatedContent);
                }
            }

            function updateDriverLocationFromResponse(data) {
                const driver = data.driver;
                const trip = data.trip;
                const location = driver.location;

                if (!location.lat || !location.lng) {
                    console.warn('Invalid driver location data received');
                    return;
                }

                // Update driver info
                $('#driverName').text(driver.name);
                $('#driverPhone').text(driver.phone);
                $('#tripId').text(trip.id);

                const statusClass = location.is_online ? 'status-online' : 'status-offline';
                const statusText = location.is_online ? 'Online' : 'Offline';
                $('#driverStatus').text(statusText).removeClass('status-online status-offline').addClass(statusClass);
                $('#lastUpdate').text(new Date(location.timestamp * 1000).toLocaleString());

                // Update coordinates
                const driverLatLng = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.lng));
                const pickupLatLng = new google.maps.LatLng(parseFloat(trip.pickup_lat), parseFloat(trip.pickup_lng));
                const dropoffLatLng = new google.maps.LatLng(parseFloat(trip.dropoff_lat), parseFloat(trip.dropoff_lng));

                if (driverMarker) {
                    driverMarker.setPosition(driverLatLng);
                    driverMarker.setAnimation(google.maps.Animation.BOUNCE);

                    setTimeout(function() {
                        if (driverMarker) {
                            driverMarker.setAnimation(null);
                        }
                    }, 2000);
                }

                if (pickupMarker) pickupMarker.setPosition(pickupLatLng);
                if (dropoffMarker) dropoffMarker.setPosition(dropoffLatLng);

                // Update directions and distances
                if (directionsService && directionsRenderer) {
                    directionsService.route({
                        origin: driverLatLng,
                        destination: dropoffLatLng,
                        waypoints: [{
                            location: pickupLatLng,
                            stopover: true
                        }],
                        travelMode: google.maps.TravelMode.DRIVING
                    }, function(result, status) {
                        if (status === 'OK') {
                            directionsRenderer.setDirections(result);
                        }
                    });
                }

                const distanceToPickup = google.maps.geometry.spherical.computeDistanceBetween(driverLatLng, pickupLatLng);
                const distanceToDropoff = google.maps.geometry.spherical.computeDistanceBetween(pickupLatLng, dropoffLatLng);
                const totalDistance = google.maps.geometry.spherical.computeDistanceBetween(driverLatLng, dropoffLatLng);

                $('#distanceToPickup').text((distanceToPickup / 1000).toFixed(2) + ' km');
                $('#distanceToDropoff').text((distanceToDropoff / 1000).toFixed(2) + ' km');
                $('#totalDistance').text((totalDistance / 1000).toFixed(2) + ' km');

                if (map) {
                    map.panTo(driverLatLng);
                }

                console.log('Driver location updated successfully');
            }

            function displayDriverLocation(data) {
                const driver = data.driver;
                const trip = data.trip;
                const location = driver.location;

                if (!location.lat || !location.lng) {
                    showError('Invalid driver location data received');
                    return;
                }

                // Update driver info
                $('#driverName').text(driver.name);
                $('#driverPhone').text(driver.phone);
                $('#tripId').text(trip.id);

                const statusClass = location.is_online ? 'status-online' : 'status-offline';
                const statusText = location.is_online ? 'Online' : 'Offline';
                $('#driverStatus').text(statusText).removeClass('status-online status-offline').addClass(statusClass);
                $('#lastUpdate').text(new Date(location.timestamp * 1000).toLocaleString());

                $('#driverInfo').show();

                // Initialize map coordinates
                const driverLatLng = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.lng));
                const pickupLatLng = new google.maps.LatLng(parseFloat(trip.pickup_lat), parseFloat(trip.pickup_lng));
                const dropoffLatLng = new google.maps.LatLng(parseFloat(trip.dropoff_lat), parseFloat(trip.dropoff_lng));

                // Initialize map only once
                if (!mapInitialized) {
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 13,
                        center: driverLatLng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        styles: [{
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{
                                visibility: "off"
                            }]
                        }]
                    });

                    // Create detailed car icon for driver
                    const carIcon = {
                        path: 'M23.5 7c.276 0 .5.224.5.5v.511c0 .793-.926.989-1.616.989l-1.086-2.068c.277-.932.68-1.932 1.138-1.932h1.064zm-1.5 2.5c0-.276.224-.5.5-.5s.5.224.5.5-.224.5-.5.5-.5-.224-.5-.5zm-13 0c0-.276.224-.5.5-.5s.5.224.5.5-.224.5-.5.5-.5-.224-.5-.5zm13.5-2.5c.276 0 .5.224.5.5v.511c0 .793-.926.989-1.616.989l-1.086-2.068c.277-.932.68-1.932 1.138-1.932h1.064z M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11C5.84 5 5.28 5.42 5.08 6.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-1.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z',
                        fillColor: '#007bff',
                        fillOpacity: 0.9,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                        scale: 1.3,
                        anchor: new google.maps.Point(12, 12)
                    };

                    // Driver marker (car icon)
                    driverMarker = new google.maps.Marker({
                        position: driverLatLng,
                        map: map,
                        title: 'Driver Current Location: ' + driver.name,
                        icon: carIcon,
                        animation: google.maps.Animation.BOUNCE
                    });

                    // Pickup location marker
                    pickupMarker = new google.maps.Marker({
                        position: pickupLatLng,
                        map: map,
                        title: 'Pickup Point: ' + (trip.pickup_name || 'Not specified'),
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });

                    // Dropoff location marker
                    dropoffMarker = new google.maps.Marker({
                        position: dropoffLatLng,
                        map: map,
                        title: 'Dropoff Point: ' + (trip.dropoff_name || 'Not specified'),
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });

                    // Info windows with Firebase data
                    const driverInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px;">
                                <h6 style="margin: 0 0 10px 0; color: #007bff;">
                                    <i class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">directions_car</i>
                                    ${driver.name}
                                </h6>
                                <p style="margin: 5px 0;"><strong>Phone:</strong> ${driver.phone}</p>
                                <p style="margin: 5px 0;"><strong>Status:</strong>
                                    <span style="color: ${location.is_online ? '#28a745' : '#dc3545'};">
                                        ${location.is_online ? 'Online' : 'Offline'}
                                    </span>
                                </p>
                                <small style="color: #6c757d;">Last Update: ${new Date(location.timestamp * 1000).toLocaleString()}</small>
                                <hr style="margin: 10px 0;">
                                <small style="color: #2196f3;"><strong> Live Firebase Data</strong></small>
                            </div>
                        `
                    });

                    // Store reference to info window for updates
                    driverMarker.infoWindow = driverInfoWindow;

                    const pickupInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px;">
                                <h6 style="margin: 0 0 10px 0; color: #28a745;">
                                    <i class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">location_on</i>
                                    Pickup Point
                                </h6>
                                <p style="margin: 0;">${trip.pickup_name || 'Location not specified'}</p>
                            </div>
                        `
                    });

                    const dropoffInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px;">
                                <h6 style="margin: 0 0 10px 0; color: #dc3545;">
                                    <i class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">flag</i>
                                    Dropoff Point
                                </h6>
                                <p style="margin: 0;">${trip.dropoff_name || 'Location not specified'}</p>
                            </div>
                        `
                    });

                    // Add click listeners
                    driverMarker.addListener('click', function() {
                        pickupInfoWindow.close();
                        dropoffInfoWindow.close();
                        driverInfoWindow.open(map, driverMarker);
                    });

                    pickupMarker.addListener('click', function() {
                        driverInfoWindow.close();
                        dropoffInfoWindow.close();
                        pickupInfoWindow.open(map, pickupMarker);
                    });

                    dropoffMarker.addListener('click', function() {
                        driverInfoWindow.close();
                        pickupInfoWindow.close();
                        dropoffInfoWindow.open(map, dropoffMarker);
                    });

                    directionsRenderer.setMap(map);
                    mapInitialized = true;
                }

                // Draw route and calculate distances
                directionsService.route({
                    origin: driverLatLng,
                    destination: dropoffLatLng,
                    waypoints: [{
                        location: pickupLatLng,
                        stopover: true
                    }],
                    travelMode: google.maps.TravelMode.DRIVING
                }, function(result, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.warn('Directions request failed due to ' + status);
                        const bounds = new google.maps.LatLngBounds();
                        bounds.extend(driverLatLng);
                        bounds.extend(pickupLatLng);
                        bounds.extend(dropoffLatLng);
                        map.fitBounds(bounds);
                    }
                });

                // Calculate distances
                const distanceToPickup = google.maps.geometry.spherical.computeDistanceBetween(driverLatLng, pickupLatLng);
                const distanceToDropoff = google.maps.geometry.spherical.computeDistanceBetween(pickupLatLng, dropoffLatLng);
                const totalDistance = google.maps.geometry.spherical.computeDistanceBetween(driverLatLng, dropoffLatLng);

                $('#distanceToPickup').text((distanceToPickup / 1000).toFixed(2) + ' km');
                $('#distanceToDropoff').text((distanceToDropoff / 1000).toFixed(2) + ' km');
                $('#totalDistance').text((totalDistance / 1000).toFixed(2) + ' km');

                // Stop driver marker animation after 3 seconds
                setTimeout(function() {
                    if (driverMarker) {
                        driverMarker.setAnimation(null);
                    }
                }, 3000);

                $('#loadingMap').hide();
                $('#map').show();
            }

            function showError(message) {
                $('#loadingMap').hide();
                $('#map').html(`
                    <div class="alert alert-danger text-center m-4">
                        <i class="material-icons-outlined" style="font-size: 48px; color: #dc3545;">error</i>
                        <h5 class="mt-3">Error Loading Location</h5>
                        <p class="mb-0">${message}</p>
                        ${typeof google === 'undefined' ? '<small class="text-muted">Please configure your Google Maps API key in the .env file</small>' : ''}
                    </div>
                `).show();
            }
        </script>
    @endif
@endpush
