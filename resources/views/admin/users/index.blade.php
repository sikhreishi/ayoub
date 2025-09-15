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
<button class="btn btn-primary mb-3" id="createUserBtn">{{ __('dashboard.users.add_new_user') }}</button>

    <div class="table-responsive">

<x-data-table 
    title="{{ __('dashboard.users.users') }}" 
    table-id="users-table" 
    fetch-url="{{ route('admin.users.data') }}"
    :columns="[__('dashboard.users.name'), __('dashboard.users.email'), __('dashboard.users.phone'), __('dashboard.users.avatar'), __('dashboard.users.actions')]"
    :columns-config="[
        ['data' => 'name', 'name' => 'name'],
        ['data' => 'email', 'name' => 'email'],
        ['data' => 'phone', 'name' => 'phone'],
        ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
    ]" 
/>


    </div>


    <!-- Modal to Create a New User -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">{{ __('dashboard.users.create_new_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        <div class="mb-3">
<label for="name" class="form-label">{{ __('dashboard.users.name') }}</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
<label for="email" class="form-label">{{ __('dashboard.users.email') }}</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
<label for="phone" class="form-label">{{ __('dashboard.users.phone') }}</label>
                            <input type="text" class="form-control" id="phone">
                        </div>

                        <div class="mb-3">
<label for="password" class="form-label">{{ __('dashboard.users.password') }}</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
<label for="password_confirmation" class="form-label">{{ __('dashboard.users.confirm_password') }}</label>
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
<label for="avatar" class="form-label">{{ __('dashboard.users.avatar') }}</label>
                            <input type="file" class="form-control" id="avatar">
                        </div>
<button type="submit" class="btn btn-primary">{{ __('dashboard.users.create') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal to Update a User -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
<h5 class="modal-title" id="updateUserModalLabel">{{ __('dashboard.users.update_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm">
                        <input type="hidden" id="update_user_id"> <!-- Hidden field for user ID -->

                        <div class="mb-3">
<label for="update_name" class="form-label">{{ __('dashboard.users.name') }}</label>
                            <input type="text" class="form-control" id="update_name" required>
                        </div>
                        <div class="mb-3">
<label for="update_email" class="form-label">{{ __('dashboard.users.email') }}</label>
                            <input type="email" class="form-control" id="update_email" required>
                        </div>
                        <div class="mb-3">
<label for="update_phone" class="form-label">{{ __('dashboard.users.phone') }}</label>
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
<label for="update_avatar" class="form-label">{{ __('dashboard.users.avatar') }}</label>
                            <input type="file" class="form-control" id="update_avatar">
                        </div>

<button type="submit" class="btn btn-primary">{{ __('dashboard.users.update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).on('TableReady', function() {

            function safeReload() {
                if (window.usersTable && typeof window.usersTable.ajax !== 'undefined') {
                    window.usersTable.ajax.reload();
                } else {
                    console.warn("usersTable is not ready yet.");
                }
            }

            // Show Create User Modal
            $('#createUserBtn').click(function() {
                $('#createUserModal').modal('show');
            });

            // Handle form submission to create a new user
            $('#createUserForm').submit(function(e) {
                e.preventDefault();

                // Collect the form data
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                // Check if password and confirm password match
                if (password !== confirmPassword) {
showToast('error', '{{ __("dashboard.users.passwords_not_match") }}');
                    return;
                }

                const formData = new FormData();
                formData.append('name', $('#name').val());
                formData.append('email', $('#email').val());
                formData.append('phone', $('#phone').val());
                formData.append('password', password); // Use the actual password
                formData.append('password_confirmation',
                confirmPassword); // Use the actual confirm password
                formData.append('country_id', $('#country_id').val());
                formData.append('avatar', $('#avatar')[0].files[0]);

                // Send the data via AJAX
                $.ajax({
                    url: '{{ route('admin.users.store') }}', // Your create driver route
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Ensure this is properly included
                    },
                    success: function(response) {
                        if (response.success) {
showToast('success', '{{ __("dashboard.users.created_success") }}');
                            $('#createUserModal').modal('hide');
                            safeReload(); // Reload the DataTable to show the new user
                        } else {
showToast('error', '{{ __("dashboard.users.created_failed") }}');
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('error', error +
                            ' An error occurred while creating the user.');
                    }
                });
            });


            $('#users-table').on('click', '.edit-item', function() {
                const userId = $(this).data('id'); // Get the user ID from the button's data attribute

                // Make AJAX request to get the user's data
                $.ajax({
                    url: '{{ route('admin.users.edit', ':id') }}'.replace(':id',
                    userId), // Fetch user data from the backend
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const user = response.data;

                            // Pre-fill the modal fields with the retrieved data
                            $('#update_user_id').val(user.id);
                            $('#update_name').val(user.name);
                            $('#update_email').val(user.email);
                            $('#update_phone').val(user.phone);
                            // You can show the avatar image if needed
                            // Example: $('#update_avatar_preview').attr('src', user.avatar);

                            // Show the modal
                            $('#updateUserModal').modal('show');
                        } else {
                            showToast('error', 'Failed to fetch user data');
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('error', 'An error occurred while fetching the user data');
                    }
                });
            });
            $('#updateUserForm').submit(function(e) {
                e.preventDefault();

                const userId = $('#update_user_id').val();
                const name = $('#update_name').val();
                const email = $('#update_email').val();
                const phone = $('#update_phone').val();

                
                const avatar = $('#update_avatar')[0].files[0]; // Handle avatar file

                const formData = new FormData();
                formData.append('_method', 'PUT'); // Method override for PUT request
                formData.append('name', name);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('country_id', $('#update_country_id').val());

                if (avatar) {
                    formData.append('avatar', avatar); // Append avatar if it's changed
                }

                $.ajax({
                    url: '{{ route('admin.users.update', ':id') }}'.replace(':id',
                    userId), // Update user route
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Ensure CSRF token is included
                    },
                    success: function(response) {
                        if (response.success) {
showToast('success', '{{ __("dashboard.users.updated_success") }}');
                            $('#updateUserModal').modal('hide');
                            safeReload(); // Reload the DataTable to show the updated user
                        } else {
showToast('error', '{{ __("dashboard.users.updated_failed") }}');
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('error', error +
                            ' An error occurred while updating the user.');
                    }
                });
            });
        });
    </script>
@endsection
