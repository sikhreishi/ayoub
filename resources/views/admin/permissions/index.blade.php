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

<!-- Info Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Permissions</h6>
                        <h4 class="mb-0" id="totalPermissionsCount">-</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="material-icons-outlined text-primary" style="font-size: 2rem;">security</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Categories</h6>
                        <h4 class="mb-0" id="categoriesCount">-</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="material-icons-outlined text-success" style="font-size: 2rem;">category</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Assigned to Roles</h6>
                        <h4 class="mb-0" id="assignedPermissionsCount">-</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="material-icons-outlined text-info" style="font-size: 2rem;">assignment</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Unassigned</h6>
                        <h4 class="mb-0" id="unassignedPermissionsCount">-</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="material-icons-outlined text-warning" style="font-size: 2rem;">warning</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Button to Add New Permission -->
{{-- <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
    <i class="material-icons-outlined me-1">add</i>
    Add New Permission
</button> --}}

<!-- Table with Bootstrap and custom styles -->
<div class="table-responsive">
    <x-data-table
        title="System Permissions"
        table-id="permissions-table"
        fetch-url="{{ route('admin.permissions.data') }}"
        :columns="['ID', 'Permission Name', 'Category', 'Roles Count', 'Created At' ]"
        :columns-config="[
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name',  ],
            ['data' => 'category', 'name' => 'category'],
            ['data' => 'roles_count', 'name' => 'roles_count', 'searchable' => false],
            ['data' => 'created_at', 'name' => 'created_at',],
            // ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>

<!-- Add Permission Modal -->
{{-- <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPermissionModalLabel">
                    <i class="material-icons-outlined me-2">add_circle</i>
                    Add New Permission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPermissionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">
                            Use format: action_resource (e.g., view_users, edit_trips, delete_drivers)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Common Permission Patterns</label>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1 permission-template" data-template="view_">View</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1 permission-template" data-template="create_">Create</button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1 permission-template" data-template="edit_">Edit</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1 permission-template" data-template="delete_">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons-outlined me-1">save</i>
                        Create Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

<!-- Edit Permission Modal -->
{{-- <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">
                    <i class="material-icons-outlined me-2">edit</i>
                    Edit Permission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editPermissionId" name="permission_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editPermissionName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="editPermissionName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons-outlined me-1">save</i>
                        Update Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

@endsection

@push('data-table-styles')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff;
    }

    .border-left-success {
        border-left: 4px solid #28a745;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107;
    }

    .permission-template {
        width: 100%;
    }
</style>
@endpush

@push('data-table-scripts')
<script>
$(document).ready(function() {
    // Wait for the DataTable to be ready
    $(document).on('TableReady', function() {
        const permissionsTable = window['permissionsTable'];

        // Update summary cards when data loads
        permissionsTable.on('xhr.dt', function(e, settings, json, xhr) {
            updateSummaryCards(json);
        });

        // Load initial data
        permissionsTable.ajax.reload();

        // Permission template buttons
        $('.permission-template').on('click', function() {
            const template = $(this).data('template');
            const currentValue = $('#permissionName').val();

            // If field is empty or already has a template, replace it
            if (!currentValue || currentValue.includes('_')) {
                $('#permissionName').val(template);
            } else {
                $('#permissionName').val(template + currentValue);
            }

            $('#permissionName').focus();
        });

        // Add Permission Form
        $('#addPermissionForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route("admin.permissions.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addPermissionModal').modal('hide');
                        $('#addPermissionForm')[0].reset();
                        permissionsTable.ajax.reload();

                        showToast('success', response.message);
                    } else {
                        handleFormErrors(response.errors, '#addPermissionForm');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showToast('error', xhr.responseJSON.message);

                }
            });
        });

        // Edit Permission
        $(document).on('click', '.edit-permission', function() {
            const permissionId = $(this).data('id');
            const permissionName = $(this).data('name');

            $('#editPermissionId').val(permissionId);
            $('#editPermissionName').val(permissionName);
            $('#editPermissionModal').modal('show');
        });

        // Update Permission Form
        $('#editPermissionForm').on('submit', function(e) {
            e.preventDefault();

            const permissionId = $('#editPermissionId').val();
            const formData = new FormData(this);

            $.ajax({
                url: `/dashboard/admin/permissions/${permissionId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editPermissionModal').modal('hide');
                        permissionsTable.ajax.reload();
                        showToast('success', response.message);

                    } else {
                        handleFormErrors(response.errors, '#editPermissionForm');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showToast('error', xhr.responseJSON.message);
                }
            });
        });

        // Delete Permission

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

    // Update summary cards
    function updateSummaryCards(data) {
        if (data.summary) {
            $('#totalPermissionsCount').text(data.summary.total || '-');
            $('#categoriesCount').text(data.summary.categories || '-');
            $('#assignedPermissionsCount').text(data.summary.assigned || '-');
            $('#unassignedPermissionsCount').text(data.summary.unassigned || '-');
        }
    }
});
</script>
@endpush
