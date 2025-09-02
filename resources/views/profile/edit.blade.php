@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush

@push('plugin-styles')
       <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />

@endpush


@extends('layouts.app')



@section('content')
<div class="container-fluid py-4">

<div class="row g-4">

    <div class="col-12 col-md-5">
<div class="card rounded-4">
    <div class="card-body p-4">
        <div class="position-relative mb-5 text-center">
            <div class="profile-avatar position-absolute top-100 start-50 translate-middle">
                @php
                    $avatarUrl = $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : ' https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff&size=32';
                @endphp
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : $avatarUrl }}"
                     class="img-fluid rounded-circle p-1 bg-grd-danger shadow"
                     style="width: 140px; height: 140px; object-fit: cover;"
                     alt="User Avatar">
            </div>
        </div>
          <div class="profile-info pt-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">


                <div class="col-md-2">

                @if ($user->hasRole('driver') && $user->driverProfile)

                                {{-- Verification Status --}}
                <label class="fw-semibold">Verified:</label><br>
                <span class="badge bg-{{ $user->driverProfile->is_driver_verified ? 'success' : 'warning' }} px-3 py-2">
                    {{ $user->driverProfile->is_driver_verified ? 'Verified' : 'Pending' }}
                </span>
                </div>



                    <div class="col-auto d-flex flex-column gap-2">
                <button class="btn btn-sm btn-success"
                        id="openDriverInfoModal"
                        data-bs-toggle="modal"
                        data-bs-target="#DriverInfoModal">
                        Driver Info
                </button>

                <button class="btn btn-sm btn-success"
                    id="openVehicleInfoModal"
                    data-bs-toggle="modal"
                    data-bs-target="#VehicleInfoModal">
                    Vehicle Info
                </button>

                @endif
                {{-- <a href="{{ route('admin.profile.wallets.show', $user->id) }}" class="btn btn-sm btn-success">
                    Wallet Info
                </a> --}}

    </div>




        </div>
    </div>
</div>

        <div class="card p-4 sm:p-8 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-4 sm:p-8 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card p-4 sm:p-8 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <!-- Right section for the DataTable -->
    <div class="col-12 col-md-7">

            <x-data-table
                title="Addresses"
                table-id="addresses-table"
                fetch-url="{{ route('admin.profile.addresses.data', $user->id) }}"
                :columns="['City', 'District', 'Street', 'Type', 'Actions']"
                :columns-config="[
                    ['data' => 'city_name', 'name' => 'city_name'],
                    ['data' => 'district_name', 'name' => 'district_name'],
                    ['data' => 'street', 'name' => 'street'],
                    ['data' => 'type', 'name' => 'type'],
                    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
                ]"
            />
        <button class="btn btn-sm btn-success" id="createAddressModalBtn" data-bs-toggle="modal" data-bs-target="#createAddressModal">Add New Address</button>


        <div class="card mt-4 p-4 shadow rounded-4">
    <h5 class="mb-3">Update Additional Information</h5>
    <form action="{{ route('profile.update.extra', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="language" class="form-label">Language</label>
                <input type="text" id="language" name="language" class="form-control" value="{{ old('language', $user->language) }}">
            </div>

            <div class="col-md-6">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-control">
                    <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="avatar" class="form-label">Avatar</label>
            <input type="file" id="avatar" name="avatar" class="form-control">
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="current_lat" class="form-label">Current Latitude</label>
                <input type="text" id="current_lat" name="current_lat" class="form-control" value="{{ old('current_lat', $user->current_lat) }}">
            </div>
            <div class="col-md-4">
                <label for="current_lng" class="form-label">Current Longitude</label>
                <input type="text" id="current_lng" name="current_lng" class="form-control" value="{{ old('current_lng', $user->current_lng) }}">
            </div>
            <div class="col-md-4">
                <label for="geohash" class="form-label">GeoHash</label>
                <input type="text" id="geohash" name="geohash" class="form-control" value="{{ old('geohash', $user->geohash) }}">
            </div>
        </div>


        <div class="row mb-3">
    <div class="col-md-6">
        <label for="country_id" class="form-label">Country</label>
        <select id="country_id" name="country_id" class="form-select rounded-3 shadow-sm">
            <option value="">Select Country</option>
            <option value="1" {{ $user->country_id == 1 ? 'selected' : '' }}>Jordan</option>
            <option value="2" {{ $user->country_id == 2 ? 'selected' : '' }}>Syria</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="city_id" class="form-label">City</label>
        <select id="city_id" name="city_id" class="form-select rounded-3 shadow-sm">
            <option value="">Select City</option>
            @if($user->country && $user->country->cities)
                @foreach($user->country->cities as $city)
                    <option value="{{ $city->id }}" {{ $user->city_id == $city->id ? 'selected' : '' }}>
                        {{ $city->name_en }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="current_address_id" class="form-label">Current Address</label>
    <select id="current_address_id" name="current_address_id" class="form-select rounded-3 shadow-sm">
        <option value="">Select Current Address</option>
        @foreach($user->addresses as $address)
            <option value="{{ $address->id }}" {{ $user->current_address_id == $address->id ? 'selected' : '' }}>
                {{ $address->city->name_en ?? 'Unknown City' }} - {{ $address->street ?? 'No Street' }} ({{ ucfirst($address->type) }})
            </option>
        @endforeach
    </select>
</div>


        <button type="submit" class="btn btn-primary">Update Info</button>
    </form>
</div>

    </div>
</div>

<div class="modal fade" id="createAddressModal" tabindex="-1" aria-labelledby="createAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAddressModalLabel">Create Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAddressForm">
                    @csrf
                    <div class="mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street_create" name="street" required>
                    </div>
                    <div class="mb-3">
                        <label for="city_id" class="form-label">City</label>
                        <select id="city_id_create" class="form-control" name="city_id" required>
                            <!-- Cities will be populated here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="district_id" class="form-label">District</label>
                        <select id="district_id_create" class="form-control" name="district_id" required>
                            <!-- Districts will be populated here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="type_create" name="type" class="form-control" required>
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="pickup">Pickup</option>
                            <option value="dropoff">Dropoff</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Address</button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="DriverInfoModal" tabindex="-1" aria-labelledby="DriverInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Wider modal for better image fit --}}
    <div class="modal-content rounded-4 shadow-sm border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="DriverInfoModalLabel">Driver Profile Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        @if ($user->driverProfile)
        <div class="container-fluid">
          <div class="row g-3">

            {{-- ID Cards --}}
            <div class="col-md-6">
              <label class="fw-semibold">ID Card (Front):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="ID Card Front">
            </div>
            <div class="col-md-6">
              <label class="fw-semibold">ID Card (Back):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->id_card_back) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="ID Card Back">
            </div>

            {{-- Driver License --}}
            <div class="col-md-6">
              <label class="fw-semibold">Driver's License (Front):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->license_front) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="License Front">
            </div>
            <div class="col-md-6">
              <label class="fw-semibold">Driver's License (Back):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->license_back) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="License Back">
            </div>

            {{-- Vehicle License --}}
            <div class="col-md-6">
              <label class="fw-semibold">Vehicle License (Front):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->vehicle_license_front) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="Vehicle License Front">
            </div>
            <div class="col-md-6">
              <label class="fw-semibold">Vehicle License (Back):</label>
              <img src="{{ asset('storage/' . $user->driverProfile->vehicle_license_back) }}"
                class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                alt="Vehicle License Back">
            </div>
            {{-- in Vehicle --}}
            <div class="col-md-6">
                <label class="fw-semibold">Vehicle Seats (Front):</label>
                <img src="{{ asset('storage/' . $user->driverProfile->interior_front_seats) }}"
                    class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                    alt="Vehicle License Front">
                </div>
                <div class="col-md-6">
                <label class="fw-semibold">Vehicle Seats (Back):</label>
                <img src="{{ asset('storage/' . $user->driverProfile->interior_back_seats) }}"
                    class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->id_card_front) }}"
                    alt="Vehicle License Back">
            </div>
                {{-- outVehicle  --}}
            <div class="col-md-6">
                <label class="fw-semibold">Out Vehicle (Front):</label>
                <img src="{{ asset('storage/' . $user->driverProfile->exterior_front_side) }}"
                  class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->exterior_front_side) }}"
                  alt="Vehicle License Front">
              </div>
              <div class="col-md-6">
                <label class="fw-semibold">Out Vehicle  (Back):</label>
                <img src="{{ asset('storage/' . $user->driverProfile->exterior_back_side) }}"
                  class="img-fluid rounded shadow-sm border clickable-image"  data-src="{{ asset('storage/' . $user->driverProfile->exterior_back_side) }}"
                  alt="Vehicle License Back">
            </div>

@role('admin') {{-- Only show for admin --}}
<form action="{{ route('profile.driver.verify', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row mt-4">
        {{-- is_driver_verified toggle --}}
        <div class="col-md-6 mb-3">
            <label for="is_driver_verified" class="form-label fw-semibold">Driver Verified:</label>
            <select name="is_driver_verified" id="is_driver_verified" class="form-select">
                <option value="1" {{ $user->driverProfile->is_driver_verified ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$user->driverProfile->is_driver_verified ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- verification_note --}}
        <div class="col-md-12 mb-3">
            <label for="verification_note" class="form-label fw-semibold">Verification Note:</label>
            <textarea name="verification_note" id="verification_note" rows="3" class="form-control"
                placeholder="Enter any note for the driver...">{{ $user->driverProfile->verification_note }}</textarea>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success">Save Verification</button>
    </div>
</form>
@endrole

          </div>
        </div>
        @else
        <p class="text-danger">No driver profile data available.</p>
        @endif
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Fullscreen Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-dark border-0">
      <div class="modal-body p-0">
        <img id="fullImage" src="" class="img-fluid w-100 rounded-0" alt="Full Image">
      </div>
      <div class="modal-footer bg-dark border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</div>













<!-- Vehicle Info Modal -->
<div class="modal fade" id="VehicleInfoModal" tabindex="-1" aria-labelledby="VehicleInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-sm border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="VehicleInfoModalLabel">Vehicle Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="vehicleInfoForm" method="POST" action="{{ route('profile.vehicle.update', $user->driverProfile->vehicle->id ?? 0) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">

          @if ($user->driverProfile && $user->driverProfile->vehicle)
            @php
              $vehicle = $user->driverProfile->vehicle;
            @endphp

            <div class="mb-3">
              <label for="make" class="form-label">Make</label>
              <input type="text" class="form-control" id="make" name="make" value="{{ old('make', $vehicle->make) }}" required>
            </div>

            <div class="mb-3">
              <label for="model" class="form-label">Model</label>
              <input type="text" class="form-control" id="model" name="model" value="{{ old('model', $vehicle->model) }}" required>
            </div>

            <div class="mb-3">
              <label for="year" class="form-label">Year</label>
              <input type="number" min="1900" max="{{ date('Y') }}" class="form-control" id="year" name="year" value="{{ old('year', $vehicle->year) }}" required>
            </div>

            <div class="mb-3">
              <label for="color" class="form-label">Color</label>
              <input type="text" class="form-control" id="color" name="color" value="{{ old('color', $vehicle->color) }}" required>
            </div>

            <div class="mb-3">
              <label for="license_plate" class="form-label">License Plate</label>
              <input type="text" class="form-control" id="license_plate" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" required>
            </div>


            <div class="mb-3">
            <label for="vehicle_type_id" class="form-label">Vehicle Type</label>
            <select id="vehicle_type_id" name="vehicle_type_id" class="form-select" required>
                @foreach($vehicleTypes as $type)
                <option value="{{ $type->id }}" {{ old('vehicle_type_id', $vehicle->vehicle_type_id) == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
                @endforeach
            </select>
            </div>

            <div class="mb-3">
              <label for="seats" class="form-label">Seats</label>
              <input type="number" min="1" max="20" class="form-control" id="seats" name="seats" value="{{ old('seats', $vehicle->seats) }}" required>
            </div>

            <div class="mb-3">
              <label for="image_url" class="form-label">Vehicle Image</label>
              <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
              @if($vehicle->image_url)
                <img src="{{ asset('storage/' . $vehicle->image_url) }}" alt="Vehicle Image" class="img-fluid mt-2 rounded" style="max-height: 150px;">
              @endif
            </div>
          @else
            <p>No vehicle information available.</p>
          @endif
        </div>
        <div class="modal-footer bg-light">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection


@push('custom-scripts')

<script>
    var $userId = @json($user->id);


 $(document).ready(function () {
    const selectedCityId = "{{ $user->city_id }}";

    $('#country_id').on('change', function () {
        var countryId = $(this).val();

        if (!countryId) {
            $('#city_id').html('<option value="">Select City</option>');
            return;
        }

        $.ajax({
            url: `/countries/${countryId}/cities`,
            type: 'GET',
            success: function (response) {
                let cityOptions = '<option value="">Select City</option>';
                response.cities.forEach(city => {
                    const selected = city.id == selectedCityId ? 'selected' : '';
                    cityOptions += `<option value="${city.id}" ${selected}>${city.name_en}</option>`;
                });
                $('#city_id').html(cityOptions);
            },
            error: function () {
                showToast('error', 'Failed to load cities');
            }
        });
    });

    if ($('#country_id').val()) {
        $('#country_id').trigger('change');
    }


     const imageModal = new bootstrap.Modal($('#imagePreviewModal')[0]);

    $('.clickable-image').css('cursor', 'zoom-in').on('click', function () {
        const imageSrc = $(this).data('src');
        $('#fullImage').attr('src', imageSrc);
        imageModal.show();
    });
});

    $(document).on('TableReady', function () {

        function safeReload() {
            if (window.addressesTable && typeof window.addressesTable.ajax !== 'undefined') {
                window.addressesTable.ajax.reload();
            } else {
                console.warn("addressesTable is not ready yet.");
            }
        }

   var modalEl = document.getElementById('createAddressModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);





    // Handle city change in create modal
    $(document).on('change', '#city_id_create', function() {
        var cityId = $(this).val();
        loadDistricts(cityId, '#district_id_create');
    });

    // Function to load districts for a city
    function loadDistricts(cityId, targetDropdown, selectedDistrictId = null) {
        if (!cityId) {
            $(targetDropdown).html('<option value="">Select District</option>');
            return;
        }

        $.ajax({
            url: '/dashboard/admin/profile/addresses/' + cityId + '/districts',
            type: 'GET',
            success: function(response) {
                var districtOptions = '<option value="">Select District</option>';
                $.each(response.districts, function(index, district) {
                    var selected = (selectedDistrictId && district.id == selectedDistrictId) ? 'selected' : '';
                    districtOptions += `<option value="${district.id}" ${selected}>${district.name_en}</option>`;
                });
                $(targetDropdown).html(districtOptions);
            }
        });
    }

    // Show create modal
    $(document).on('click', '#createAddressModalBtn', function() {
        // Fetch cities when modal opens
        $.ajax({
            url: '/dashboard/admin/profile/addresses/' + $userId + '/create',
            type: 'GET',
            success: function(response) {
                var cityOptions = '<option value="">Select City</option>';
                $.each(response.cities, function(index, city) {
                    cityOptions += `<option value="${city.id}">${city.name_en}</option>`;
                });
                $('#city_id_create').html(cityOptions);
                $('#district_id_create').html('<option value="">Select District</option>');
            }
        });


    });

    $('#createAddressModal').on('shown.bs.modal', function() {
        this.removeAttribute('aria-hidden');
        this.querySelector('.modal-content').removeAttribute('aria-hidden');
    });

    $('#createAddressModal').on('hidden.bs.modal', function() {
        this.setAttribute('aria-hidden', 'true');
        this.querySelector('.modal-content').setAttribute('aria-hidden', 'true');
    });


    $('#createAddressForm').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: '/dashboard/admin/profile/addresses/' + $userId + '/store',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('createAddressModal'));
                    modal.hide();
                        showToast('success', 'Address created successfully!');
                   safeReload();
                } else {
                    showToast('Failed to create address');
                }
            },
            error: function(response) {
                showToast('Failed to create address: ' + response.responseJSON.message);
            }
        });
    });
});
</script>

@endpush
