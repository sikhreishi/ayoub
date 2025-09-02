@extends('layouts.app')

@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush

@section('content')
    <div class="d-flex gap-2 mb-3">
        <button class="btn btn-primary" id="createVehicleTypeBtn">Add New Vehicle Type</button>
        <button class="btn btn-warning" id="manageCommissionBtn">Manage Commission</button>
    </div>

    <div class="table-responsive">
        <x-data-table title="Vehicle Types" table-id="vehicle-types-table"
            fetch-url="{{ route('admin.vehicle_types.data') }}" :columns="['Name', 'Description', 'Commission', 'Status', 'Actions']" :columns-config="[
                ['data' => 'name'],
                ['data' => 'description'],
                ['data' => 'commission'],
                ['data' => 'status'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <!-- Modal: Create -->
    <div class="modal fade" id="createVehicleTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="createVehicleTypeForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Vehicle Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.vehicles.types._form-fields')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Update -->
    <div class="modal fade" id="updateVehicleTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="updateVehicleTypeForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vehicle Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="update_vehicle_type_id">
                    @include('admin.vehicles.types._form-fields', ['prefix' => 'update_'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Commission Management -->
    <div class="modal fade" id="commissionModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="commissionForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Commission Percentage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="commission_percentage" class="form-label">Commission Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="commission_percentage"
                                name="commission_percentage" min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">This percentage will be applied to all vehicle types globally.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update Commission</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        $(document).on('TableReady', function() {
            function reloadTable() {
                if (window.vehicleTypesTable?.ajax) {
                    window.vehicleTypesTable.ajax.reload();
                }
            }

            $('#createVehicleTypeBtn').click(() => {
                $('#createVehicleTypeForm')[0].reset();
                $('#createVehicleTypeModal').modal('show');
            });

            $('#manageCommissionBtn').click(() => {
                $.ajax({
                    url: '{{ route('admin.vehicle_types.commission.get') }}',
                    method: 'GET',
                    success(response) {
                        if (response.success) {
                            $('#commission_percentage').val(response.commission_percentage);
                            $('#commissionModal').modal('show');
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error(xhr) {
                        showToast('error', xhr.responseJSON?.message ||
                            'Error fetching commission data');
                    }
                });
            });

            $('#createVehicleTypeForm').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.vehicle_types.store') }}',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            $('#createVehicleTypeModal').modal('hide');
                            reloadTable();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Error occurred');
                    }
                });
            });

            $('#commissionForm').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.vehicle_types.commission.update') }}',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            $('#commissionModal').modal('hide');
                            reloadTable();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Error occurred');
                    }
                });
            });

            $('#vehicle-types-table').on('click', '.edit-item', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: '{{ route('admin.vehicle_types.edit', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    success(response) {
                        const data = response.data;
                        $('#update_vehicle_type_id').val(data.id);
                        $('#update_name').val(data.name);
                        $('#update_description').val(data.description);
                        $('#update_start_fare').val(data.start_fare);
                        $('#update_day_per_km_rate').val(data.day_per_km_rate);
                        $('#update_night_per_km_rate').val(data.night_per_km_rate);
                        $('#update_day_per_minute_rate').val(data.day_per_minute_rate);
                        $('#update_night_per_minute_rate').val(data.night_per_minute_rate);
                        $('#update_is_active').val(data.is_active ? 1 : 0);
                        $('#update_icon_url').val('');

                        if (data.icon_url) {
                            if ($('#icon-preview').length === 0) {
                                $('#update_icon_url').after(
                                    '<div id="icon-preview" class="mt-2"></div>');
                            }
                            $('#icon-preview').html(
                                `<img src="${data.icon_url}" alt="Icon" width="50" />`);
                        } else {
                            $('#icon-preview').html('No icon uploaded');
                        }

                        $('#updateVehicleTypeModal').modal('show');
                    },
                    error() {
                        showToast('error', 'Failed to load vehicle type');
                    }
                });
            });

            $('#updateVehicleTypeForm').submit(function(e) {
                e.preventDefault();
                const id = $('#update_vehicle_type_id').val();
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ route('admin.vehicle_types.update', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            $('#updateVehicleTypeModal').modal('hide');
                            reloadTable();
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Update failed');
                    }
                });
            });
        });
    </script>
@endpush
