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
    <button class="btn btn-primary mb-3" id="createDriverBtn">Add New Driver</button>

<!-- Table with Bootstrap and custom styles -->
<div class="table-responsive">

    <x-data-table
        title="Drivers"
        table-id="drivers-table"
        fetch-url="{{ route('admin.drivers.data') }}"
        :columns="['Name', 'Email', 'Phone', 'Avatar', 'Actions']"
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
        <h5 class="modal-title" id="createDriverModalLabel">Create New Driver</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createDriverForm">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone">
          </div>
          <div class="mb-3">
            <label for="language" class="form-label">Language</label>
            <input type="text" class="form-control" id="language">
          </div>
         <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" required>
         </div>
         <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" required>
         </div>

          <div class="mb-3">
            <label for="avatar" class="form-label">Avatar</label>
            <input type="file" class="form-control" id="avatar">
          </div>
          <button type="submit" class="btn btn-primary">Create Driver</button>
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
        <h5 class="modal-title" id="updateDriverModalLabel">Update Driver</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateDriverForm">
          <input type="hidden" id="update_driver_id"> <!-- Hidden field for driver ID -->

          <div class="mb-3">
            <label for="update_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="update_name" required>
          </div>
          <div class="mb-3">
            <label for="update_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="update_email" required>
          </div>
          <div class="mb-3">
            <label for="update_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="update_phone">
          </div>
          <div class="mb-3">
            <label for="update_language" class="form-label">Language</label>
            <input type="text" class="form-control" id="update_language">
          </div>
          <div class="mb-3">
            <label for="update_avatar" class="form-label">Avatar</label>
            <input type="file" class="form-control" id="update_avatar">
          </div>

          <button type="submit" class="btn btn-primary">Update Driver</button>
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
                showToast('error', 'Passwords do not match!');
                return;
            }

            const formData = new FormData();
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
            formData.append('language', $('#language').val());
            formData.append('password', password);  // Use the actual password
            formData.append('password_confirmation', confirmPassword); // Use the actual confirm password
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
                        showToast('success', 'Driver created successfully!');
                        $('#createDriverModal').modal('hide');
                        safeReload();

                    } else {
                        showToast('error', response.message || 'Failed to create driver');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', error + ' An error occurred while creating the driver.');
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
                $('#update_language').val(driver.language);
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
        const language = $('#update_language').val();
            const avatar = $('#update_avatar')[0].files[0]; // Handle avatar file

            const formData = new FormData();
            formData.append('_method', 'PUT');  // Method override for PUT request
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('language', language);
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
