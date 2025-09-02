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
    <!-- Button to Add New Role -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="material-icons-outlined me-1">add</i>
        Add New Role
    </button>

    <!-- Table with Bootstrap and custom styles -->
    <div class="table-responsive">
        <x-data-table title="System Roles" table-id="roles-table" fetch-url="{{ route('admin.roles.data') }}"
            :columns="['ID', 'Role Name', 'Users Count', 'Permissions Count', 'Created At', 'Actions']" :columns-config="[
                ['data' => 'id', 'name' => 'id'],
                ['data' => 'name', 'name' => 'name'],
                ['data' => 'users_count', 'name' => 'users_count', 'searchable' => false],
                ['data' => 'permissions_count', 'name' => 'permissions_count', 'searchable' => false],
                ['data' => 'created_at', 'name' => 'created_at'],
                ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">
                        <i class="material-icons-outlined me-2">add_circle</i>
                        Add New Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRoleForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Use lowercase with underscores (e.g., content_manager, customer_support)
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined me-1">save</i>
                            Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">
                        <i class="material-icons-outlined me-2">edit</i>
                        Edit Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRoleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editRoleId" name="role_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRoleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="editRoleName" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined me-1">save</i>
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirm-delete-modal />
@endsection

@push('data-table-scripts')
    <script>
        $(document).ready(function() {
            // Wait for the DataTable to be ready
            $(document).on('TableReady', function() {
                const rolesTable = window['rolesTable'];

                // Add Role Form
                $('#addRoleForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    $.ajax({
                        url: '{{ route('admin.roles.store') }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#addRoleModal').modal('hide');
                                $('#addRoleForm')[0].reset();
                                rolesTable.ajax.reload();
                                showToast('success', response.message);
                            } else {
                                handleFormErrors(response.errors, '#addRoleForm');
                            }
                        },
                        error: function(xhr) {
                            showToast('error', xhr.responseJSON.message);
                        }
                    });
                });

                // Edit Role
                $(document).on('click', '.edit-role', function() {
                    const roleId = $(this).data('id');
                    const roleName = $(this).data('name');

                    $('#editRoleId').val(roleId);
                    $('#editRoleName').val(roleName);
                    $('#editRoleModal').modal('show');
                });

                // Update Role Form
                $('#editRoleForm').on('submit', function(e) {
                    e.preventDefault();

                    const roleId = $('#editRoleId').val();
                    const formData = new FormData(this);

                    $.ajax({
                        url: `/dashboard/admin/roles/${roleId}`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#editRoleModal').modal('hide');
                                rolesTable.ajax.reload();
                                showToast('success', response.message);


                            } else {
                                handleFormErrors(response.errors, '#editRoleForm');
                            }
                        },
                        error: function(xhr) {
                            showToast('error', xhr.responseJSON.message);
                        }
                    });
                });

                // Delete Role
                let roleToDelete = null;
                
                $(document).on('click', '.delete-role', function() {
                    const roleId = $(this).data('id');
                    const roleName = $(this).data('name');
                    
                    // Store role data for deletion
                    roleToDelete = { id: roleId, name: roleName };
                    
                    // Update modal message
                    $('#confirmDeleteModal .modal-body').html(
                        `Are you sure you want to delete the role "<strong>${roleName}</strong>"? This action cannot be undone.`
                    );
                    
                    // Show the modal
                    $('#confirmDeleteModal').modal('show');
                });
                
                // Handle delete confirmation
                $(document).on('click', '#confirmDeleteBtn', function() {
                    if (roleToDelete) {
                        $.ajax({
                            url: `/dashboard/admin/roles/${roleToDelete.id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#confirmDeleteModal').modal('hide');
                                if (response.success) {
                                    rolesTable.ajax.reload();
                                    showToast('success', response.message);
                                } else {
                                    showToast('error', response.message);
                                }
                                roleToDelete = null;
                            },
                            error: function(xhr) {
                                $('#confirmDeleteModal').modal('hide');
                                console.error('Error:', xhr);
                                showToast('error', xhr.responseJSON?.message || 'An error occurred');
                                roleToDelete = null;
                            }
                        });
                    }
                });

                // Handle form errors
                function handleFormErrors(errors, formSelector) {
                    // Clear previous errors
                    $(formSelector + ' .is-invalid').removeClass('is-invalid');
                    $(formSelector + ' .invalid-feedback').text('');

                    // Display new errors
                    $.each(errors, function(field, messages) {
                        const input = $(formSelector + ' [name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                }

                // Clear form errors when modal is hidden
                $('.modal').on('hidden.bs.modal', function() {
                    $(this).find('.is-invalid').removeClass('is-invalid');
                    $(this).find('.invalid-feedback').text('');
                });
            });
        });
    </script>
@endpush
