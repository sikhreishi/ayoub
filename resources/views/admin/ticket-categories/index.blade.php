@extends('layouts.app')

@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            display: inline-block;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }
        .delete-modal .modal-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }

        .table-actions .btn {
            margin: 0 2px;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Enhanced Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                                data-bs-target="#categoryModal">
                                <i class="fas fa-plus me-2"></i>Add Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Categories Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <x-data-table title="Categories List" table-id="categories-table"
                                fetch-url="{{ route('admin.ticket_categories.data') }}" :columns="['Name', 'Color', 'Description', 'Tickets', 'Status', 'Order', 'Actions']"
                                :columns-config="[
                                    ['data' => 'name', 'name' => 'name'],
                                    ['data' => 'color_preview', 'name' => 'color', 'orderable' => false],
                                    ['data' => 'description', 'name' => 'description'],
                                    ['data' => 'tickets_count', 'name' => 'tickets_count'],
                                    ['data' => 'status_badge', 'name' => 'is_active'],
                                    ['data' => 'sort_order', 'name' => 'sort_order'],
                                    [
                                        'data' => 'actions',
                                        'name' => 'actions',
                                        'orderable' => false,
                                        'searchable' => false,
                                    ],
                                ]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">
                        <i class="fas fa-tag me-2"></i>Add Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="categoryId">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label fw-semibold">
                                        <i class="fas fa-signature text-primary me-1"></i>Name *
                                    </label>
                                    <input type="text" id="categoryName" name="name"
                                        class="form-control form-control-lg" placeholder="Enter category name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="categorySortOrder" class="form-label fw-semibold">
                                        <i class="fas fa-sort-numeric-up text-primary me-1"></i>Sort Order
                                    </label>
                                    <input type="number" id="categorySortOrder" name="sort_order"
                                        class="form-control form-control-lg" value="0" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label fw-semibold">
                                <i class="fas fa-align-left text-primary me-1"></i>Description
                            </label>
                            <textarea id="categoryDescription" name="description" class="form-control" rows="3"
                                placeholder="Enter category description"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoryColor" class="form-label fw-semibold">
                                        <i class="fas fa-palette text-primary me-1"></i>Color *
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <input type="color" id="categoryColor" name="color"
                                            class="form-control form-control-color me-3" value="#007bff" required>
                                        <span class="text-muted">Choose category color</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-toggle-on text-primary me-1"></i>Status
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="categoryIsActive" name="is_active"
                                            class="form-check-input" checked>
                                        <label class="form-check-label" for="categoryIsActive">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow delete-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2">Are you sure?</h5>
                    <p class="text-muted mb-0">You want to delete this category?</p>
                    <p class="text-muted small">This action cannot be undone and may affect related tickets.</p>
                </div>
                <div class="modal-footer justify-content-center bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i>Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        let categoryToDelete = null;

        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Wait for DataTable to be ready
            $(document).on('TableReady', function() {
                const table = window['categoriesTable'];
                if (table) {
                    table.order([
                        [5, 'asc']
                    ]).draw();
                }
            });

            // Form submit handler with loading state
            $('#categoryForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i>Saving...');

                saveCategory().finally(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                });
            });

            // Reset modal when closed
            $('#categoryModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            // Confirm delete handler
            $('#confirmDeleteBtn').click(function() {
                if (categoryToDelete) {
                    const btn = $(this);
                    const originalText = btn.html();

                    btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...');

                    performDelete(categoryToDelete).finally(() => {
                        btn.prop('disabled', false).html(originalText);
                        $('#deleteModal').modal('hide');
                        categoryToDelete = null;
                    });
                }
            });

            // Reset delete modal when closed
            $('#deleteModal').on('hidden.bs.modal', function() {
                categoryToDelete = null;
            });
        });

        function editCategory(id) {
            return $.get(`{{ route('admin.ticket_categories.show', ':id') }}`.replace(':id', id))
                .done(function(response) {
                    if (response.success) {
                        const category = response.data;
                        $('#categoryId').val(category.id);
                        $('#categoryName').val(category.name);
                        $('#categoryDescription').val(category.description);
                        $('#categoryColor').val(category.color);
                        $('#categorySortOrder').val(category.sort_order);
                        $('#categoryIsActive').prop('checked', category.is_active);
                        $('#categoryModalTitle').html('<i class="fas fa-edit me-2"></i>Edit Category');
                        $('#categoryModal').modal('show');
                    }
                })
                .fail(function() {
                    showToast('error', 'Failed to load category data');
                });
        }

        function deleteCategory(id) {
            categoryToDelete = id;
            $('#deleteModal').modal('show');
        }

        function performDelete(id) {
            return $.ajax({
                    url: `{{ route('admin.ticket_categories.destroy', ':id') }}`.replace(':id', id),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        const table = window['categoriesTable'];
                        if (table) {
                            table.ajax.reload();
                        }
                        showToast('success', response.message || 'Category deleted successfully');
                    } else {
                        showToast('error', response.message || 'Failed to delete category');
                    }
                })
                .fail(function() {
                    showToast('error', 'An error occurred while deleting the category');
                });
        }

        function saveCategory() {
            const id = $('#categoryId').val();
            const url = id ?
                `{{ route('admin.ticket_categories.update', ':id') }}`.replace(':id', id) :
                `{{ route('admin.ticket_categories.store') }}`;
            const method = id ? 'PUT' : 'POST';

            const formData = {
                name: $('#categoryName').val().trim(),
                description: $('#categoryDescription').val().trim(),
                color: $('#categoryColor').val(),
                sort_order: $('#categorySortOrder').val() || 0,
                is_active: $('#categoryIsActive').is(':checked') ? 1 : 0
            };

            return $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        $('#categoryModal').modal('hide');
                        const table = window['categoriesTable'];
                        if (table) {
                            table.ajax.reload();
                        }
                        showToast('success', response.message || 'Category saved successfully');
                    } else {
                        showToast('error', response.message || 'Failed to save category');
                    }
                })
                .fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessage = '';
                        Object.keys(xhr.responseJSON.errors).forEach(key => {
                            errorMessage += xhr.responseJSON.errors[key].join(', ') + '\n';
                        });
                        showToast('error', errorMessage.trim());
                    } else {
                        showToast('error', 'An error occurred while saving the category');
                    }
                });
        }

        function resetForm() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#categoryModalTitle').html('<i class="fas fa-tag me-2"></i>Add Category');
            $('#categoryColor').val('#007bff');
            $('#categoryIsActive').prop('checked', true);
            $('#categorySortOrder').val(0);
        }
    </script>
@endpush


