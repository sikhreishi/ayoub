
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

<button class="btn btn-primary mb-3" id="createDistrictBtn">Add New District</button>

<div class="table-responsive">
<x-data-table 
    title="Districts"
    table-id="districts-table"
    fetch-url="{{ route('admin.districts.data') }}"
    :columns="['Name (EN)', 'Name (AR)', 'City', 'Latitude', 'Longitude', 'Created At', 'Actions']"
    :columns-config="[
        ['data' => 'name_en', 'name' => 'name_en'],
        ['data' => 'name_ar', 'name' => 'name_ar'],
        ['data' => 'city_name', 'name' => 'city_name'],
        ['data' => 'lat', 'name' => 'lat'],
        ['data' => 'lng', 'name' => 'lng'],
        ['data' => 'created_at', 'name' => 'created_at'],
        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
    ]"
/>

</div>

<!-- Modal to Create a New District -->
<div class="modal fade" id="createDistrictModal" tabindex="-1" aria-labelledby="createDistrictModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createDistrictModalLabel">Create New District</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createDistrictForm">
          <div class="mb-3">
            <label for="name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="name_en" required>
          </div>
          <div class="mb-3">
            <label for="name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="name_ar">
          </div>
          <div class="mb-3">
            <label for="city_id" class="form-label">City</label>
            <select class="form-control" id="city_id" required>
                <!-- Cities will be populated here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="lat" class="form-label">Latitude</label>
            <input type="text" class="form-control" id="lat">
          </div>
          <div class="mb-3">
            <label for="lng" class="form-label">Longitude</label>
            <input type="text" class="form-control" id="lng">
          </div>
          <button type="submit" class="btn btn-primary">Create District</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal to Update a District -->
<div class="modal fade" id="updateDistrictModal" tabindex="-1" aria-labelledby="updateDistrictModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateDistrictModalLabel">Update District</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateDistrictForm">
          <input type="hidden" id="update_district_id">

          <div class="mb-3">
            <label for="update_name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="update_name_en" required>
          </div>
          <div class="mb-3">
            <label for="update_name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="update_name_ar">
          </div>
          <div class="mb-3">
            <label for="update_city_id" class="form-label">City</label>
            <select class="form-control" id="update_city_id" required>
                <!-- Cities will be populated here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="update_lat" class="form-label">Latitude</label>
            <input type="text" class="form-control" id="update_lat">
          </div>
          <div class="mb-3">
            <label for="update_lng" class="form-label">Longitude</label>
            <input type="text" class="form-control" id="update_lng">
          </div>

          <button type="submit" class="btn btn-primary">Update District</button>
        </form>
      </div>
    </div>
  </div>
</div>





@endsection


@push('custom-scripts')
  
<script>
    $(document).on('TableReady', function () {

        function safeReload() {
            if (window.districtsTable && typeof window.districtsTable.ajax !== 'undefined') {
                window.districtsTable.ajax.reload();
            } else {
                console.warn("districtsTable is not ready yet.");
            }
        }
        
        $('#createDistrictBtn').click(function() {
            $('#createDistrictModal').modal('show');
            populateCities(); 
        });

        $('#createDistrictForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name_en', $('#name_en').val());
            formData.append('name_ar', $('#name_ar').val());
            formData.append('city_id', $('#city_id').val());
            formData.append('lat', $('#lat').val());
            formData.append('lng', $('#lng').val());

            // Send the data via AJAX
            $.ajax({
                url: '{{ route('admin.districts.store') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'District created successfully!');
                        $('#createDistrictModal').modal('hide');
                        safeReload(); // Reload the DataTable
                    } else {
                        showToast('error', response.message || 'Failed to create district');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while creating the district.');
                }
            });
        });

        // Show Update District Modal
        $('#districts-table').on('click', '.edit-item', function() {
            const districtId = $(this).data('id'); // Get the district ID from the button's data attribute

            // Make AJAX request to get the district data
            $.ajax({
                url: '{{ route('admin.districts.edit', ':id') }}'.replace(':id', districtId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const district = response.data;

                        // Pre-fill the modal fields with the retrieved data
                        $('#update_district_id').val(district.id);
                        $('#update_name_en').val(district.name_en);
                        $('#update_name_ar').val(district.name_ar);
                        $('#update_city_id').val(district.city_id);
                        $('#update_lat').val(district.lat);
                        $('#update_lng').val(district.lng);

                        // Show the modal
                        $('#updateDistrictModal').modal('show');
                        populateCities(district.city_id); // Pre-select the city
                    } else {
                        showToast('error', 'Failed to fetch district data');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'An error occurred while fetching the district data');
                }
            });
        });

        // Handle form submission to update the district
        $('#updateDistrictForm').submit(function(e) {
            e.preventDefault();

            const districtId = $('#update_district_id').val();
            const name_en = $('#update_name_en').val();
            const name_ar = $('#update_name_ar').val();
            const city_id = $('#update_city_id').val();
            const lat = $('#update_lat').val();
            const lng = $('#update_lng').val();

            const formData = new FormData();
            formData.append('_method', 'PUT'); // Method override for PUT request
            formData.append('name_en', name_en);
            formData.append('name_ar', name_ar);
            formData.append('city_id', city_id);
            formData.append('lat', lat);
            formData.append('lng', lng);

            // Send the data via AJAX
            $.ajax({
                url: '{{ route('admin.districts.update', ':id') }}'.replace(':id', districtId),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'District updated successfully!');
                        $('#updateDistrictModal').modal('hide');
                        safeReload(); // Reload the DataTable
                    } else {
                        showToast('error', response.message || 'Failed to update district');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while updating the district.');
                }
            });
        });

        // Function to populate cities in select dropdown
        function populateCities(selectedCityId = '') {
            $.ajax({
                url: '{{ route('admin.cities.getAll') }}', // Route to fetch cities
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const cities = response.data;
                        let options = '<option value="">Select City</option>';
                        cities.forEach(city => {
                            options += `<option value="${city.id}" ${city.id == selectedCityId ? 'selected' : ''}>${city.name_en}</option>`;
                        });
                        $('#city_id, #update_city_id').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Error fetching cities');
                }
            });
        }

    });
</script>
@endpush