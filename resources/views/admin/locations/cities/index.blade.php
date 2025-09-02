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

<button class="btn btn-primary mb-3" id="createCityBtn">Add New City</button>

<div class="table-responsive">
    <x-data-table 
        title="Cities"
        table-id="cities-table"
        fetch-url="{{ route('admin.cities.data') }}"
        :columns="['Name (EN)', 'Name (AR)', 'Country', 'Latitude', 'Longitude', 'Created At', 'Actions']"
        :columns-config="[
            ['data' => 'name_en', 'name' => 'name_en'],
            ['data' => 'name_ar', 'name' => 'name_ar'],
            ['data' => 'country.name_en', 'name' => 'country.name_en'],
            ['data' => 'lat', 'name' => 'lat'],
            ['data' => 'lng', 'name' => 'lng'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />


</div>

<!-- Modal to Create a New City -->
<div class="modal fade" id="createCityModal" tabindex="-1" aria-labelledby="createCityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCityModalLabel">Create New City</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createCityForm">
          <div class="mb-3">
            <label for="name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="name_en" required>
          </div>
          <div class="mb-3">
            <label for="name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="name_ar" required>
          </div>
          <div class="mb-3">
            <label for="country_id" class="form-label">Country</label>
            <select class="form-control" id="country_id" required>
                <!-- Dynamically populated countries -->
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
          <button type="submit" class="btn btn-primary">Create City</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal to Update a City -->
<div class="modal fade" id="updateCityModal" tabindex="-1" aria-labelledby="updateCityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateCityModalLabel">Update City</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateCityForm">
          <input type="hidden" id="update_city_id"> <!-- Hidden field for city ID -->

          <div class="mb-3">
            <label for="update_name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="update_name_en" required>
          </div>
          <div class="mb-3">
            <label for="update_name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="update_name_ar" required>
          </div>
          <div class="mb-3">
            <label for="update_country_id" class="form-label">Country</label>
            <select class="form-control" id="update_country_id" required>
                <!-- Dynamically populated countries -->
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

          <button type="submit" class="btn btn-primary">Update City</button>
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
            if (window.citiesTable && typeof window.citiesTable.ajax !== 'undefined') {
                window.citiesTable.ajax.reload();
            } else {
                console.warn("citiesTable is not ready yet.");
            }
        }

        $('#createCityBtn').click(function() {
            $('#createCityModal').modal('show');
            populateCountries(); 
        });

        $('#createCityForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name_en', $('#name_en').val());
            formData.append('name_ar', $('#name_ar').val());
            formData.append('country_id', $('#country_id').val());
            formData.append('lat', $('#lat').val());
            formData.append('lng', $('#lng').val());

            $.ajax({
                url: '{{ route('admin.cities.store') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'City created successfully!');
                        $('#createCityModal').modal('hide');
                        safeReload(); 
                    } else {
                        showToast('error', response.message || 'Failed to create city');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while creating the city.');
                }
            });
        });

        $('#cities-table').on('click', '.edit-item', function() {
            const cityId = $(this).data('id'); 

            $.ajax({
                url: '{{ route('admin.cities.edit', ':id') }}'.replace(':id', cityId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const city = response.data;

                        $('#update_city_id').val(city.id);
                        $('#update_name_en').val(city.name_en);
                        $('#update_name_ar').val(city.name_ar);
                        $('#update_country_id').val(city.country_id);
                        $('#update_lat').val(city.lat);
                        $('#update_lng').val(city.lng);

                        $('#updateCityModal').modal('show');
                        populateCountries(city.country_id); 
                    } else {
                        showToast('error', 'Failed to fetch city data');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'An error occurred while fetching the city data');
                }
            });
        });

        $('#updateCityForm').submit(function(e) {
            e.preventDefault();

            const cityId = $('#update_city_id').val();
            const name_en = $('#update_name_en').val();
            const name_ar = $('#update_name_ar').val();
            const country_id = $('#update_country_id').val();
            const lat = $('#update_lat').val();
            const lng = $('#update_lng').val();

            const formData = new FormData();
            formData.append('_method', 'PUT'); 
            formData.append('name_en', name_en);
            formData.append('name_ar', name_ar);
            formData.append('country_id', country_id);
            formData.append('lat', lat);
            formData.append('lng', lng);

            $.ajax({
                url: '{{ route('admin.cities.update', ':id') }}'.replace(':id', cityId),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'City updated successfully!');
                        $('#updateCityModal').modal('hide');
                        safeReload();
                    } else {
                        showToast('error', response.message || 'Failed to update city');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while updating the city.');
                }
            });
        });

        function populateCountries(selectedCountryId = '') {
            $.ajax({
                url: '{{ route('admin.countries.getAll') }}', 
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const countries = response.data;
                        let options = '<option value="">Select Country</option>';
                        countries.forEach(country => {
                            options += `<option value="${country.id}" ${country.id == selectedCountryId ? 'selected' : ''}>${country.name_en}</option>`;
                        });
                        $('#country_id, #update_country_id').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Error fetching countries');
                }
            });
        }

    });
</script>

@endpush
