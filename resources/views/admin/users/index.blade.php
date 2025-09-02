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
    <button class="btn btn-primary mb-3" id="createUserBtn">Add New User</button>

    <div class="table-responsive">
        <x-data-table title="Users" table-id="users-table" fetch-url="{{ route('admin.users.data') }}" :columns="['Name', 'Email', 'Phone', 'Language', 'Gender', 'Avatar', 'Actions']"
            :columns-config="[
                ['data' => 'name', 'name' => 'name'],
                ['data' => 'email', 'name' => 'email'],
                ['data' => 'phone', 'name' => 'phone'],
                ['data' => 'language', 'name' => 'language'],
                ['data' => 'gender', 'name' => 'gender'],
                ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />

    </div>


    <!-- Modal to Create a New User -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
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


    <!-- Modal to Update a User -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm">
                        <input type="hidden" id="update_user_id"> <!-- Hidden field for user ID -->

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
                    showToast('error', 'Passwords do not match!');
                    return;
                }

                const formData = new FormData();
                formData.append('name', $('#name').val());
                formData.append('email', $('#email').val());
                formData.append('phone', $('#phone').val());
                formData.append('language', $('#language').val());
                formData.append('password', password); // Use the actual password
                formData.append('password_confirmation',
                confirmPassword); // Use the actual confirm password
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
                            showToast('success', 'User created successfully!');
                            $('#createUserModal').modal('hide');
                            safeReload(); // Reload the DataTable to show the new user
                        } else {
                            showToast('error', response.message || 'Failed to create user');
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
                            $('#update_language').val(user.language);
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
                const language = $('#update_language').val();
                const avatar = $('#update_avatar')[0].files[0]; // Handle avatar file

                const formData = new FormData();
                formData.append('_method', 'PUT'); // Method override for PUT request
                formData.append('name', name);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('language', language);
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
                            showToast('success', 'User updated successfully!');
                            $('#updateUserModal').modal('hide');
                            safeReload(); // Reload the DataTable to show the updated user
                        } else {
                            showToast('error', response.message || 'Failed to update user');
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
