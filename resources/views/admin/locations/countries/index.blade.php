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

<!-- Button to Add New Country -->
<button class="btn btn-primary mb-3" id="createCountryBtn">Add New Country</button>

<!-- Table with Bootstrap and custom styles -->
<div class="table-responsive">
<x-data-table 
    title="Countries"
    table-id="countries-table"
    fetch-url="{{ route('admin.countries.data') }}"
    :columns="['Name (EN)', 'Name (AR)', 'Code', 'Created At', 'Actions']"
    :columns-config="[
        ['data' => 'name_en', 'name' => 'name_en'],
        ['data' => 'name_ar', 'name' => 'name_ar'],
        ['data' => 'code', 'name' => 'code'],
        ['data' => 'created_at', 'name' => 'created_at'],
        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
    ]"
/>
</div>

<!-- Modal to Create a New Country -->
<div class="modal fade" id="createCountryModal" tabindex="-1" aria-labelledby="createCountryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCountryModalLabel">Create New Country</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createCountryForm">
          <div class="mb-3">
            <label for="name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="name_en" required>
          </div>
          <div class="mb-3">
            <label for="name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="name_ar" required>
          </div>
          <div class="mb-3">
            <label for="code" class="form-label">Country Code</label>
            <input type="text" class="form-control" id="code" required maxlength="3">
          </div>
          <button type="submit" class="btn btn-primary">Create Country</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal to Update a Country -->
<div class="modal fade" id="updateCountryModal" tabindex="-1" aria-labelledby="updateCountryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateCountryModalLabel">Update Country</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateCountryForm">
          <input type="hidden" id="update_country_id"> <!-- Hidden field for country ID -->

          <div class="mb-3">
            <label for="update_name_en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="update_name_en" required>
          </div>
          <div class="mb-3">
            <label for="update_name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="update_name_ar" required>
          </div>
          <div class="mb-3">
            <label for="update_code" class="form-label">Country Code</label>
            <input type="text" class="form-control" id="update_code" required maxlength="3">
          </div>

          <button type="submit" class="btn btn-primary">Update Country</button>
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
            if (window.countriesTable && typeof window.countriesTable.ajax !== 'undefined') {
                window.countriesTable.ajax.reload();
            } else {
                console.warn("countriesTable is not ready yet.");
            }
        }

        // Show Create Country Modal
        $('#createCountryBtn').click(function() {
            $('#createCountryModal').modal('show');
        });

        // Handle form submission to create a new country
        $('#createCountryForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name_en', $('#name_en').val());
            formData.append('name_ar', $('#name_ar').val());
            formData.append('code', $('#code').val());

            // Send the data via AJAX
            $.ajax({
                url: '{{ route('admin.countries.store') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Country created successfully!');
                        $('#createCountryModal').modal('hide');
                       safeReload(); // Reload the DataTable
                    } else {
                        showToast('error', response.message || 'Failed to create country');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while creating the country.');
                }
            });
        });

        // Show Update Country Modal
        $('#countries-table').on('click', '.edit-item', function() {
            const countryId = $(this).data('id'); // Get the country ID from the button's data attribute

            // Make AJAX request to get the country data
            $.ajax({
                url: '{{ route('admin.countries.edit', ':id') }}'.replace(':id', countryId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const country = response.data;

                        // Pre-fill the modal fields with the retrieved data
                        $('#update_country_id').val(country.id);
                        $('#update_name_en').val(country.name_en);
                        $('#update_name_ar').val(country.name_ar);
                        $('#update_code').val(country.code);

                        // Show the modal
                        $('#updateCountryModal').modal('show');
                    } else {
                        showToast('error', 'Failed to fetch country data');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'An error occurred while fetching the country data');
                }
            });
        });

        // Handle form submission to update the country
        $('#updateCountryForm').submit(function(e) {
            e.preventDefault();

            const countryId = $('#update_country_id').val();
            const name_en = $('#update_name_en').val();
            const name_ar = $('#update_name_ar').val();
            const code = $('#update_code').val();

            const formData = new FormData();
            formData.append('_method', 'PUT'); // Method override for PUT request
            formData.append('name_en', name_en);
            formData.append('name_ar', name_ar);
            formData.append('code', code);

            // Send the data via AJAX
            $.ajax({
                url: '{{ route('admin.countries.update', ':id') }}'.replace(':id', countryId),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Country updated successfully!');
                        $('#updateCountryModal').modal('hide');
                       safeReload(); // Reload the DataTable
                    } else {
                        showToast('error', response.message || 'Failed to update country');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while updating the country.');
                }
            });
        });
    });
</script>
@endpush