@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Manage Permissions for Role: <span class="text-primary">{{ ucfirst($role->name) }}</span></h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAll">
                <i class="material-icons-outlined me-1">select_all</i>
                Select All
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAll">
                <i class="material-icons-outlined me-1">deselect</i>
                Deselect All
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="material-icons-outlined me-1">arrow_back</i>
                Back to Roles
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="material-icons-outlined me-2">security</i>
                Assign Permissions
            </h5>
        </div>
        <div class="card-body">
            <form id="rolePermissionsForm">
                @csrf
                <div class="row">
                    @foreach ($permissions as $category => $categoryPermissions)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="material-icons-outlined me-2">folder</i>
                                        {{ $category }}
                                        <span class="badge bg-secondary ms-auto">{{ $categoryPermissions->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input category-select-all" type="checkbox"
                                                id="selectAll{{ $category }}" data-category="{{ $category }}">
                                            <label class="form-check-label fw-bold" for="selectAll{{ $category }}">
                                                Select All {{ $category }}
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    @foreach ($categoryPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="permissions[]" value="{{ $permission->name }}"
                                                id="permission{{ $permission->id }}" data-category="{{ $category }}"
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission{{ $permission->id }}">
                                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="text-muted">
                                <small>
                                    <i class="material-icons-outlined me-1" style="font-size: 16px;">info</i>
                                    Changes will be applied immediately after saving
                                </small>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined me-1">save</i>
                                    Save Permissions
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="material-icons-outlined me-2">analytics</i>
                Permission Summary
            </h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h4 class="text-primary mb-1" id="totalPermissions">{{ $permissions->flatten()->count() }}</h4>
                        <small class="text-muted">Total Permissions</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h4 class="text-success mb-1" id="assignedPermissions">{{ $role->permissions->count() }}</h4>
                        <small class="text-muted">Assigned</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h4 class="text-warning mb-1" id="unassignedPermissions">
                            {{ $permissions->flatten()->count() - $role->permissions->count() }}</h4>
                        <small class="text-muted">Unassigned</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <h4 class="text-info mb-1">{{ $permissions->count() }}</h4>
                        <small class="text-muted">Categories</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .permission-checkbox {
            margin-right: 8px;
        }

        .category-select-all {
            margin-right: 8px;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .text-center .border {
            transition: all 0.3s ease;
        }

        .text-center .border:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('plugin-scripts')
    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#selectAll').on('click', function() {
                $('.permission-checkbox').prop('checked', true);
                $('.category-select-all').prop('checked', true);
                updateSummary();
            });

            // Deselect All functionality
            $('#deselectAll').on('click', function() {
                $('.permission-checkbox').prop('checked', false);
                $('.category-select-all').prop('checked', false);
                updateSummary();
            });

            // Category Select All functionality
            $('.category-select-all').on('change', function() {
                const category = $(this).data('category');
                const isChecked = $(this).is(':checked');

                $(`.permission-checkbox[data-category="${category}"]`).prop('checked', isChecked);
                updateSummary();
            });

            // Individual permission checkbox change
            $('.permission-checkbox').on('change', function() {
                const category = $(this).data('category');
                const categoryCheckboxes = $(`.permission-checkbox[data-category="${category}"]`);
                const checkedInCategory = categoryCheckboxes.filter(':checked').length;
                const totalInCategory = categoryCheckboxes.length;

                // Update category select all checkbox
                const categorySelectAll = $(`.category-select-all[data-category="${category}"]`);
                if (checkedInCategory === 0) {
                    categorySelectAll.prop('checked', false).prop('indeterminate', false);
                } else if (checkedInCategory === totalInCategory) {
                    categorySelectAll.prop('checked', true).prop('indeterminate', false);
                } else {
                    categorySelectAll.prop('checked', false).prop('indeterminate', true);
                }

                updateSummary();
            });

            // Initialize category checkboxes state
            $('.category-select-all').each(function() {
                const category = $(this).data('category');
                const categoryCheckboxes = $(`.permission-checkbox[data-category="${category}"]`);
                const checkedInCategory = categoryCheckboxes.filter(':checked').length;
                const totalInCategory = categoryCheckboxes.length;

                if (checkedInCategory === 0) {
                    $(this).prop('checked', false).prop('indeterminate', false);
                } else if (checkedInCategory === totalInCategory) {
                    $(this).prop('checked', true).prop('indeterminate', false);
                } else {
                    $(this).prop('checked', false).prop('indeterminate', true);
                }
            });

            // Update summary counters
            function updateSummary() {
                const totalPermissions = $('.permission-checkbox').length;
                const assignedPermissions = $('.permission-checkbox:checked').length;
                const unassignedPermissions = totalPermissions - assignedPermissions;

                $('#totalPermissions').text(totalPermissions);
                $('#assignedPermissions').text(assignedPermissions);
                $('#unassignedPermissions').text(unassignedPermissions);
            }

            // Form submission
            $('#rolePermissionsForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="material-icons-outlined me-1">hourglass_empty</i>Saving...').prop(
                    'disabled', true);

                $.ajax({
                    url: '{{ route('admin.roles.permissions.update', $role->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showToast('error', xhr.responseJSON.message);
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Initialize summary on page load
            updateSummary();
        });
    </script>
@endpush
