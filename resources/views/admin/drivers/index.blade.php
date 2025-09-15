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


<!-- Button to Add New Driver -->
   <button class="btn btn-primary mb-3" id="createDriverBtn">
    {{ __('dashboard.drivers.add_new_driver') }}
</button>

<!-- Table with Bootstrap and custom styles -->
<div class="table-responsive">

    <x-data-table
    title="{{ __('dashboard.users.drivers') }}" 
        table-id="drivers-table"
        fetch-url="{{ route('admin.drivers.data') }}"
        :columns="[__('dashboard.users.name'), __('dashboard.users.email'), __('dashboard.users.phone'), __('dashboard.users.avatar'), __('dashboard.users.actions')]"
        :columns-config="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />

</div>

<!-- Modal to Create a New Driver -->
<div class="modal fade" id="createDriverModal" tabindex="-1" aria-labelledby="createDriverModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
<h5 class="modal-title" id="createDriverModalLabel">{{ __('dashboard.drivers.create_new_driver') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createDriverForm">
          <div class="mb-3">
<label for="name" class="form-label">{{ __('dashboard.drivers.name') }}</label>
            <input type="text" class="form-control" id="name" required>
          </div>
          <div class="mb-3">
<label for="email" class="form-label">{{ __('dashboard.drivers.email') }}</label>
            <input type="email" class="form-control" id="email" required>
          </div>
          <div class="mb-3">
<label for="phone" class="form-label">{{ __('dashboard.drivers.phone') }}</label>
            <input type="text" class="form-control" id="phone">
          </div>
         
         <div class="mb-3">
<label for="password" class="form-label">{{ __('dashboard.drivers.password') }}</label>
            <input type="password" class="form-control" id="password" required>
         </div>
         <div class="mb-3">
<label for="password_confirmation" class="form-label">{{ __('dashboard.drivers.confirm_password') }}</label>
            <input type="password" class="form-control" id="password_confirmation" required>
         </div>
                                 <div class="mb-3">
<label for="country_id" class="form-label">{{ __('dashboard.users.country') }}</label>
                            <select class="form-select" id="country_id" required>
                            <option value="">{{ __('dashboard.users.select_country') }}</option>
@foreach ($countries as $country)
    <option value="{{ $country->id }}">
        {{ app()->getLocale() === 'ar' ? $country->name_ar : $country->name_en }}
    </option>
@endforeach
                            </select>
                        </div>

          <div class="mb-3">
<label for="avatar" class="form-label">{{ __('dashboard.drivers.avatar') }}</label>
            <input type="file" class="form-control" id="avatar">
          </div>
<button type="submit" class="btn btn-primary">{{ __('dashboard.drivers.create') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal to Update a Driver -->
<div class="modal fade" id="updateDriverModal" tabindex="-1" aria-labelledby="updateDriverModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
<h5 class="modal-title" id="updateDriverModalLabel">{{ __('dashboard.drivers.update_driver') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateDriverForm">
          <input type="hidden" id="update_driver_id"> <!-- Hidden field for driver ID -->

          <div class="mb-3">
<label for="update_name" class="form-label">{{ __('dashboard.drivers.name') }}</label>
            <input type="text" class="form-control" id="update_name" required>
          </div>
          <div class="mb-3">
<label for="update_email" class="form-label">{{ __('dashboard.drivers.email') }}</label>
            <input type="email" class="form-control" id="update_email" required>
          </div>
          <div class="mb-3">
<label for="update_phone" class="form-label">{{ __('dashboard.drivers.phone') }}</label>
            <input type="text" class="form-control" id="update_phone">
          </div>
                        <div class="mb-3">
<label for="update_country_id" class="form-label">{{ __('dashboard.users.country') }}</label>
                            <select class="form-select" id="update_country_id" required>
<option value="">{{ __('dashboard.users.select_country') }}</option>
@foreach ($countries as $country)
    <option value="{{ $country->id }}">
        {{ app()->getLocale() === 'ar' ? $country->name_ar : $country->name_en }}
    </option>
@endforeach
                            </select>
                        </div>
          <div class="mb-3">
<label for="update_avatar" class="form-label">{{ __('dashboard.drivers.avatar') }}</label>
            <input type="file" class="form-control" id="update_avatar">
          </div>

<button type="submit" class="btn btn-primary">{{ __('dashboard.drivers.update') }}</button>
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
            if (window.driversTable && typeof window.driversTable.ajax !== 'undefined') {
                window.driversTable.ajax.reload();
            } else {
                console.warn("driversTable is not ready yet.");
            }
        }


        // Show Create Driver Modal
        $('#createDriverBtn').click(function() {
            $('#createDriverModal').modal('show');
        });

     // Handle form submission to create a new driver
        $('#createDriverForm').submit(function(e) {
            e.preventDefault();

            // Collect the form data
            const password = $('#password').val();
            const confirmPassword = $('#password_confirmation').val();

            // Check if password and confirm password match
            if (password !== confirmPassword) {
    showToast('error', '{{ __("dashboard.drivers.passwords_not_match") }}');
                return;
            }

            const formData = new FormData();
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
            formData.append('password', password);  // Use the actual password
            formData.append('password_confirmation', confirmPassword); // Use the actual confirm password
            formData.append('country_id', $('#country_id').val());
            formData.append('avatar', $('#avatar')[0].files[0]);

            // Send the data via AJAX
            $.ajax({
                url: '{{ route('admin.drivers.store') }}', // Your create driver route
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure this is properly included
                },
                success: function(response) {
                    if (response.success) {
        showToast('success', '{{ __("dashboard.drivers.created_success") }}');
                        $('#createDriverModal').modal('hide');
                        safeReload();

                    } else {
        showToast('error', '{{ __("dashboard.drivers.created_failed") }}');
                    }
                },
                error: function(xhr, status, error) {
    showToast('error', '{{ __("dashboard.drivers.error_occurred") }}');
                }
            });
        });


$('#drivers-table').on('click', '.edit-item', function() {
    const driverId = $(this).data('id'); // Get the driver ID from the button's data attribute

    // Make AJAX request to get the driver's data
    $.ajax({
        url: '{{ route('admin.drivers.edit', ':id') }}'.replace(':id', driverId), // Fetch driver data from the backend
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const driver = response.data;

                // Pre-fill the modal fields with the retrieved data
                $('#update_driver_id').val(driver.id);
                $('#update_name').val(driver.name);
                $('#update_email').val(driver.email);
                $('#update_phone').val(driver.phone);
                $('#update_country_id').val(driver.country_id);
                // You can show the avatar image if needed
                // Example: $('#update_avatar_preview').attr('src', driver.avatar);

                // Show the modal
                $('#updateDriverModal').modal('show');
            } else {
                showToast('error', 'Failed to fetch driver data');
                
            }
        },
        error: function(xhr, status, error) {
            showToast('error', 'An error occurred while fetching the driver data');
        }
    });
});
    $('#updateDriverForm').submit(function(e) {
        e.preventDefault();

        const driverId = $('#update_driver_id').val();
        const name = $('#update_name').val();
        const email = $('#update_email').val();
        const phone = $('#update_phone').val();
            const countryId = $('#update_country_id').val();
            const avatar = $('#update_avatar')[0].files[0]; // Handle avatar file

            const formData = new FormData();
            formData.append('_method', 'PUT');  // Method override for PUT request
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('country_id', countryId);
            if (avatar) {
                formData.append('avatar', avatar);  // Append avatar if it's changed
            }

            $.ajax({
                url: '{{ route('admin.drivers.update', ':id') }}'.replace(':id', driverId), // Update driver route
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
                },
               success: function(response) {
                    if (response.success) {
                        showToast('success', 'Driver updated successfully!');
                        $('#updateDriverModal').modal('hide');
                        safeReload();
                    } else {
                        showToast('error', response.message || 'Failed to update driver');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while updating the driver.');
                }
            });
        });
    });
</script>


@endpush
